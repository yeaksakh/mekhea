<?php

namespace Modules\ExpenseAutoFill\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Storage;
use Modules\ExpenseAutoFill\Entities\ExpenseAutoFill;
use Modules\ExpenseAutoFill\Entities\ExpenseAutoFillSocial;
use Modules\ExpenseAutoFill\Entities\TelegramExpenseImageData;

class ExpenseInvoiceOcrService
{
    protected $botToken;
    protected $apiUrl;
    protected $googleApiKey;


    public function __construct()
    {
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent';
        $this->googleApiKey = 'AIzaSyB8QQJbsEh8WUTNuqoCZW7_UHTYqXYvUVo';
    }

    /**
     * Extract structured purchase invoice data from image
     */
    public function extractInvoiceData($imageContent, array $telegramData, $botToken)
    {
          // Get business ID from bot token
                $businessId = ExpenseAutoFillSocial::where('social_token', $botToken)
                    ->value('business_id');

        $prompt = $this->buildInvoiceExtractionPrompt($businessId);
        $maxRetries = 3;
        $retryCount = 0;

        while ($retryCount < $maxRetries) {
            try {
                // Check if Google API key is configured
                if (empty($this->googleApiKey)) {
                    throw new Exception('Google API key not configured. Please set GOOGLE_GEMINI_API_KEY in your .env file or services.google.gemini_api_key in your config.');
                }
                if (empty($imageContent)) {
                    throw new Exception('No image content');
                }

              

                if (!$businessId) {
                    throw new Exception('Business not found for the provided bot token');
                }

                // Use Google API key for OCR request
                $response = Http::timeout(30)->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($this->apiUrl . '?key=' . $this->googleApiKey, [
                    'contents' => [[
                        'parts' => [
                            ['inlineData' => ['mimeType' => 'image/jpeg', 'data' => $imageContent]],
                            ['text' => $prompt]
                        ]
                    ]]
                ]);

                // Check if we hit rate limit
                if ($response->status() === 429) {
                    $retryCount++;
                    $waitTime = pow(2, $retryCount) * 5; // Exponential backoff: 5s, 10s, 20s

                    Log::warning('Rate limit hit, retrying', [
                        'attempt' => $retryCount,
                        'wait_time' => $waitTime,
                        'response' => $response->body()
                    ]);

                    // Wait before retrying
                    sleep($waitTime);
                    continue;
                }

                if (!$response->successful()) {
                    throw new Exception('API failed: ' . $response->body());
                }

                $aiText = $response->json('candidates.0.content.parts.0.text') ?? '';
                if (empty($aiText)) throw new Exception('Empty AI response');

                $parsed = $this->parseAndCleanResponse($aiText);

                // Find existing record
                $imageData = TelegramExpenseImageData::where('telegram_file_unique_id', $telegramData['file_unique_id'])
                    ->where('business_id', $businessId)
                    ->first();

                \Log::debug("trransaction date" . $parsed['data']['transaction_date']);

                if ($imageData) {
                    // Update the record with OCR results
                    $imageData->update([
                        'total_amount' => $parsed['data']['total_amount'],
                        'transaction_date' => $parsed['data']['transaction_date'],
                        'supplier' => $parsed['data']['supplier'],
                        'location' => $parsed['data']['location'],
                        'category' => $parsed['data']['category'],
                        'sub_category' => $parsed['data']['sub_category'],
                        'tax' => $parsed['data']['tax'],
                        'expense_for' => $parsed['data']['expense_for'],
                        'ref_no' => $parsed['data']['ref_no'],
                        'notes' => $parsed['data']['notes'],
                        'employee_name' => $parsed['data']['employee_name'],
                        'status' => 'processed'
                    ]);
                }

                return $parsed;
            } catch (Exception $e) {
                // If this is our last retry attempt, give up
                if ($retryCount >= $maxRetries - 1) {
                    Log::error('OCR failed after all retries', ['error' => $e->getMessage()]);
                    return [
                        'success' => false,
                        'message' => $e->getMessage(),
                        'data' => null
                    ];
                }

                // For other errors, also retry with exponential backoff
                $retryCount++;
                $waitTime = pow(2, $retryCount) * 5;

                Log::warning('Error in OCR, retrying', [
                    'attempt' => $retryCount,
                    'wait_time' => $waitTime,
                    'error' => $e->getMessage()
                ]);

                sleep($waitTime);
            }
        }
    }
    /**
     * Build specific prompt for invoice data extraction
     */

    private function buildInvoiceExtractionPrompt($businessId)
    {

        $prompt = ExpenseAutoFillSocial::where('business_id', $businessId)->value('prompt');
        return $prompt;
        // return <<<PROMPT
        //     'ocr_invoice' => 'Extract from the invoice image as **pure JSON only** (no extra text, no Markdown):
        //     {
        //         "total_amount": {"value": numeric, "currency": "symbol/code or null"},
        //         "transaction_date": "DD/MM/YYYY or null",
        //         "supplier": "exact supplier name or null",
        //         "location": "business location or null",
        //         "category": "expense category or null",
        //         "sub_category": "sub-category or null",
        //         "tax": "tax rate/name or null",
        //         "expense_for": "expense for name or null",
        //         "ref_no": "invoice/reference number or null",
        //         "notes": "notes or terms or null",
        //         "employee_name": "If an EMP pattern is found (e.g., EMP-123), return the corresponding employee name (e.g., វី ឡុងដេត). For EMP-135, return វី ឡុងដេត. If no EMP pattern is found or the employee cannot be identified, return null."
        //     }

        //     **NEVER guess Khmer text. Use null if unclear. Prefer null over guessing for all textual fields.**

        //     **NEVER guess Khmer text. Use null if unclear. Prefer null over guessing for all textual fields.**

        //     ────────────────────────────────────────────────
        //     CUSTOM RULE (EDC ELECTRICITY INVOICE HANDLING)
        //     ────────────────────────────────────────────────
        //     If the supplier or header indicates electricity authority:
        //     • "EDC"
        //     • "Electricite du Cambodge"
        //     • "Electricité du Cambodge"
        //     • " - អគ្គិសនីកម្ពុជា(CO4510)"
        //     • Or typical EDC bill structure (meter readings, kWh, usage, etc.)

        //     THEN APPLY:

        //     1) supplier:
        //         " - អគ្គិសនីកម្ពុជា(CO4510)"

        //     2) category:
        //         "ចំណាយទឹកភ្លើង"

        //     3) notes:
        //         "បង់ភ្លើង"

        //     4) If rounded total exists → use rounded total amount.
        //     Ignore subtotal if rounded payable amount is present.

        //     5) expense_for rule:
        //         If customer name = "NET YARA" → expense_for = "លួន សុណា"
        //         Otherwise → expense_for = "staff in company"

        //     6) location rule:
        //         If customer name = "NET YARA" → location = "ឃ្លាំង 41"
        //         Otherwise → null

        //     7) sub_category and tax → null unless explicitly shown.

        //     8) transaction_date (VERY IMPORTANT):
        //         EDC invoices contain multiple dates.
        //         ONLY use the date located in the TOP-RIGHT section of the invoice 
        //         (usually next to the invoice number, labeled “Invoice Date”).
        //         DO NOT use billing period dates.
        //         DO NOT use payment due dates.
        //         DO NOT use any other date anywhere else.

        //     EDC logic OVERRIDES ALL FUEL LOGIC, VET LOGIC, AND CAPITOL LOGIC.


        //     ────────────────────────────────────────────────
        //     HANDWRITTEN SUPPLIER LOGIC (រម៉កពូទ្រី)
        //     ────────────────────────────────────────────────
        //     If the invoice contains CLEARLY READABLE handwritten text **matching exactly "រម៉កពូទ្រី"**  
        //     (allow small handwriting variations, but must be confidently readable):

        //         expense_for: "ផល្ល័ក្ខ សុបុរិន្ទ្រ"
        //         location: "ឃ្លាំង 41"
        //         supplier: "ពូទ្រី - រម៉កដឹកជញ្ជូន(CO8792)"
        //         category: "ចំណាយដឹកជញ្ជូន(ទំនិញ)"
        //         notes: "រម៉កពូទ្រី"

        //     If the handwriting is unclear, low-confidence, partially cropped, or ambiguous →  
        //     **do NOT apply this rule. Keep these fields = null.**


        //     ────────────────────────────────────────────────
        //     HIGHEST PRIORITY: Vehicle Code Detection (A001 / A002 / A003)
        //     (Only applies when NOT an EDC invoice)
        //     ────────────────────────────────────────────────
        //     Search entire invoice for:
        //     a001 / a-001 / A001 / A-001 → Gasoline (សាំង)
        //     a002 / a-002 / A002 / A-002 → Diesel (ម៉ាស៊ូត)
        //     a003 / a-003 / A003 / A-003 → Gas/LPG (ហ្គាស)

        //     If any A-code is found → FUEL = TRUE:
        //         location: "ឃ្លាំង 41"
        //         category: "ចំណាយសាំង  ម៉ាស៊ូត ហ្គាស"

        //         notes:
        //             A001 → "ចាក់សាំងរម៉ក"
        //             A002 → "ចាក់ម៉ាស៊ូត"
        //             A003 → "ចាក់ហ្គាសរថយន្ត"

        //         supplier (A001/A002):
        //             Use brand detection rules.
        //         supplier (A003):
        //             Force → "ស្ថានីយហ្គាស - (CO5271)"

        //     A-code overrides all non-A-code rules.


        //     ────────────────────────────────────────────────
        //     STRICT FUEL DETECTION (Station-word REQUIRED)
        //     (Only applies when NOT A-code and NOT EDC)
        //     ────────────────────────────────────────────────
        //     Station-word REQUIRED:
        //     • "ស្ថានីយ" 
        //     • "ស្ថានីយ៍"
        //     • "station"

        //     Fuel = TRUE only if BOTH:
        //     (1) Station word detected
        //     (2) Strong confirmation:

        //     STRONG CONFIRMATION:
        //     1) fuel_brand + fuel_word
        //     2) fuel_word + station_word
        //     3) fuel_brand + realistic quantity (e.g., “3.75L”)
        //     4) Clear explicit fuel text: “fuel”, “gas station”, “pump”, “ចាក់សាំង”, …

        //     If station word NOT detected → DO NOT classify as fuel.

        //     FUEL WORDS:
        //     សាំង, ម៉ាស៊ូត, ហ្គាស, gas, GAS, LPG

        //     BRANDS:
        //     TOTAL, TELA, TETA, CALTEX, SOKIMEX, PPT, LY HEANG,
        //     តូតាល់, តេលា, កាល់តិច, សូគីម៉ិច, ភីធីធី, លី ហ៊ាង

        //     ────────────────────────────────────────────────
        //     CONTEXT-AWARE BRAND FILTERING & SAFETY RULES
        //     ────────────────────────────────────────────────
        //     • Ignore “TOTAL” in final total box (not a fuel brand).
        //     • Require spatial proximity between brand + fuel word.
        //     • Reject low-confidence OCR text (<0.60) unless A-code seen.
        //     • Do not classify fuel from a single indicator alone.
        //     • Avoid Khmer normalization when unclear (return null).

        //     ────────────────────────────────────────────────
        //     FUEL OUTPUT RULES (when confirmed fuel)
        //     ────────────────────────────────────────────────
        //     location: "ឃ្លាំង 41"
        //     category: "ចំណាយសាំង  ម៉ាស៊ូត ហ្គាស"

        //     notes priority:
        //         gas/LPG → "ចាក់ហ្គាសរថយន្ត"
        //         diesel → "ចាក់ម៉ាស៊ូត"
        //         gasoline → "ចាក់សាំងរម៉ក"
        //         fallback → "ចាក់ប្រេងឥន្ទនៈ"

        //     supplier mapping:
        //         តេលា/TELA/TETA         → "ស្ថានីយប្រេងឥន្ធនះ​​ កម្ពុជាតេលា - ស្ថានីយប្រេងឥន្ធនះ​​ កម្ពុជាតេលា(CO4751)"
        //         តូតាល់/TOTAL           → "ស្ថានីយប្រេងឥន្ធនះតូតាល់ - ស្ថានីយប្រេងឥន្ធនះ តូតាល់(CO4533)"
        //         លី ហ៊ាង / LY HEANG       → "ស្ថានីយប្រេងឥន្ធនះ លី ហ៊ាង(876)"
        //         កាល់តិច/CALTEX          → "ស្ថានីយប្រេងឥន្ធនះ កាល់តិច(CO4904)"
        //         សូគីម៉ិច/SOKIMEX        → "ស្ថានីយប្រេងឥន្ធនះ ស៊ូគីមិច(CO4963)"
        //         ភីធីធី/PPT             → "ស្ថានីយប្រេងឥន្ធនះ ភីធីធី(1231)"
        //         សាវីម៉ិច/SAVIMEX         → " - ស្ថានីយប្រេងឥន្តនះសាវីមិុចព្រីមៀរ(CO4701)"
        //         Gas/LPG only            → "ស្ថានីយហ្គាស - (CO5271)"
        //         otherwise               → "ស្ថានីយប្រេងឥន្ធនះ - (CO5375)"


        //     ────────────────────────────────────────────────
        //     1. VET EXPRESS invoices
        //     ────────────────────────────────────────────────
        //     If text contains “VET EXPRESS” or “វីរៈប៊ុនថាំ អេចប្រេស”:

        //     If “ផ្ញើពីសាខា …” then read branch name.

        //     • "សាខាផ្សារជ្រាវ" / "សៀមរាប)SR":
        //         expense_for: "នួន ចន្ទី"
        //         location: "វត្តបូព៌"
        //         supplier: "វីរះប៊ុនថាំសាខាសៀមរាប"
        //         category: "ចំណាយដឹកជញ្ជូន(ទំនិញ)"
        //         notes: "វីរះប៊ុនថាំ សាខាផ្សារជ្រាវ(សៀមរាប)SR ទៅ"

        //     • "PP ទួលស្ងែ":
        //         expense_for: "ផល្ល័ក្ខ សុបុរិន្ទ្រ"
        //         location: "ឃ្លាំង 41"
        //         supplier: "វីរះប៊ុនថាំសាខាសុផារ៉ា"
        //         category: "ចំណាយដឹកជញ្ជូន(ទំនិញ)"
        //         notes: "វីរះប៊ុនថាំ PP ទួលសង្កែ ទៅ"


        //     ────────────────────────────────────────────────
        //     2. CAPITOL CAMBODIA invoices
        //     ────────────────────────────────────────────────
        //     If text contains “CAPITOL CAMBODIA” or “ក្រុមហ៊ុន កាពីតូល”:

        //     If “គោលដៅ …” then read the destination.

        //     • "ភ្នំពេញ(សាខាកាំកូ)":
        //         expense_for: "ផល្ល័ក្ខ សុបុរិន្ទ្រ"
        //         location: "ឃ្លាំង 41"
        //         supplier: " - ក្រុមហ៊ុនកាពីតូល(សាខាកាំកូ)ភ្នំពេញ(33)"
        //         category: "ចំណាយដឹកជញ្ជូន(ទំនិញ)"
        //         notes: "ក្រុមហ៊ុនកាពីតូល ភ្នំពេញ(សាខាកាំកូ) ទៅ"

        //     • "សៀមរាប(ក្របីរៀល)":
        //         expense_for: "នួន ចន្ទី"
        //         location: "វត្តបូព៌"
        //         supplier: " - ក្រុមហ៊ុន​ កាពីតូល(សៀមរាប)(CO4515)"
        //         category: "ចំណាយដឹកជញ្ជូន(ទំនិញ)"
        //         notes: "ក្រុមហ៊ុនកាពីតូល សៀមរាប(ក្របីរៀល) ទៅ"


        //     ────────────────────────────────────────────────
        //     FINAL RULES
        //     ────────────────────────────────────────────────
        //     • EDC rules override everything else.
        //     • A-code has highest priority only when NOT EDC.
        //     • For non-A-code fuel, station word is mandatory.
        //     • VET and CAPITOL rules override general logic.
        //     • Always return pure JSON only.
        //     • If uncertain → use null for safety.
        //     '
        // PROMPT;
    }


    /**
     * Parse AI response and clean it up
     */
    private function parseAndCleanResponse($aiResponse)
    {
        try {
            // Log the raw response for debugging
            Log::info('Raw AI response', [
                'response' => substr($aiResponse, 0, 500) . '...' // Log first 500 chars
            ]);

            // Clean the response to get pure JSON
            $cleanJson = $this->extractJsonFromResponse($aiResponse);

            Log::info('Cleaned JSON extracted', [
                'original_length' => strlen($aiResponse),
                'cleaned_length' => strlen($cleanJson),
                'cleaned_json_preview' => substr($cleanJson, 0, 200) . '...'
            ]);

            // Parse JSON
            $parsedData = json_decode($cleanJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON parsing failed', [
                    'error' => json_last_error_msg(),
                    'json' => $cleanJson
                ]);

                return [
                    'success' => false,
                    'message' => 'Invalid JSON response from AI: ' . json_last_error_msg(),
                    'data' => null,
                    'raw_response' => $aiResponse
                ];
            }

            // Clean and validate the data
            $cleanedData = $this->cleanExtractedData($parsedData);

            return [
                'success' => true,
                'message' => 'Invoice data extracted successfully',
                'data' => $cleanedData,
                'raw_response' => $aiResponse // Include for debugging
            ];
        } catch (Exception $e) {
            Log::error('Response parsing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'response' => substr($aiResponse, 0, 500) . '...' // Log first 500 chars
            ]);

            return [
                'success' => false,
                'message' => 'Failed to parse AI response: ' . $e->getMessage(),
                'data' => null,
                'raw_response' => $aiResponse
            ];
        }
    }

    /**
     * Extract JSON from AI response (remove markdown, extra text, etc.)
     */
    private function extractJsonFromResponse($response)
    {
        // Remove markdown code blocks
        $response = preg_replace('/```json\s*/', '', $response);
        $response = preg_replace('/```\s*/', '', $response);

        // Remove any text before the first {
        $response = preg_replace('/^[^{]*/', '', $response);

        // Remove any text after the last }
        $lastBrace = strrpos($response, '}');
        if ($lastBrace !== false) {
            $response = substr($response, 0, $lastBrace + 1);
        }

        // Extract JSON object using regex
        if (preg_match('/\{.*\}/s', $response, $matches)) {
            return trim($matches[0]);
        }

        // If no valid JSON found, log the issue
        Log::warning('No valid JSON found in response', [
            'response_preview' => substr($response, 0, 200) . '...'
        ]);

        return trim($response);
    }

    /**
     * Clean and validate extracted data
     */
    /**
     * Clean and validate extracted data
     */
    /**
     * Clean and validate extracted data
     */
    private function cleanExtractedData($data)
    {
        $cleaned = [];

        // Define all expected fields with default null values
        // These should match the fields from your AI prompt
        $expectedFields = [
            'total_amount',
            'transaction_date',
            'supplier',
            'location',
            'category',
            'sub_category',
            'tax',
            'expense_for',
            'ref_no',
            'notes',
            'employee_name'
        ];

        // Process each field
        foreach ($expectedFields as $field) {
            if (!isset($data[$field])) {
                $cleaned[$field] = null;
                continue;
            }

            // Apply specific cleaning based on field type
            switch ($field) {
                case 'total_amount':
                    $rawValue = null;
                    $currency = 'USD'; // default

                    if (is_array($data[$field] ?? null)) {
                        $rawValue = $data[$field]['value'] ?? null;
                        $currency = strtoupper(trim($data[$field]['currency'] ?? 'USD'));
                    } elseif (is_numeric($data[$field] ?? null) || is_string($data[$field] ?? null)) {
                        $rawValue = $data[$field];
                    }

                    $amount = $this->cleanAmount($rawValue);

                    if ($amount !== null && $amount > 0) {
                        // Only divide if NOT USD or $ (i.e. it's in Riel)
                        $isUsd = in_array($currency, ['USD', '$', 'DOLLAR', 'DOLLARS']);

                        if (! $isUsd) {
                            $amount = $amount / 4000; // Convert Riel → USD equivalent
                        }

                        $cleaned[$field] = round((float)$amount, 2);
                    } else {
                        $cleaned[$field] = null;
                    }
                    break;

                case 'transaction_date':
                    $cleaned[$field] = $this->cleanDate($data[$field]);
                    break;

                case 'employee_id':
                    $cleaned[$field] = $this->cleanInteger($data[$field]);
                    break;

                default:
                    // For all other fields, use string cleaning
                    $cleaned[$field] = $this->cleanString($data[$field]);
            }
        }

        return $cleaned;
    }
    /**
     * Clean amount field
     */
    private function cleanAmount($amount)
    {
        if ($amount === null || $amount === '' || $amount === 'null') {
            return null;
        }

        // Remove everything except digits, decimal point, and minus sign
        $clean = preg_replace('/[^\d.-]/', '', (string)$amount);

        // Convert to float
        $value = floatval($clean);

        // BLOCK NEGATIVE VALUES — return null if negative
        return $value < 0 ? null : $value;
    }

    /**
     * Clean integer field
     */
    private function cleanInteger($value)
    {
        if ($value === null || $value === '' || $value === 'null') {
            return null;
        }

        // Remove any non-numeric characters
        $cleanValue = preg_replace('/[^\d]/', '', $value);

        // Convert to integer
        return intval($cleanValue);
    }

    /**
     * Clean date field
     */
    private function cleanDate($date)
    {
        if (empty($date) || $date === 'null') {
            return null;
        }

        try {
            // Try multiple date formats that might appear in OCR
            $formats = [
                'd/m/Y',  // DD/MM/YYYY
                'd-m-Y',  // DD-MM-YYYY
                'Y-m-d',  // YYYY-MM-DD
                'm/d/Y',  // MM/DD/YYYY (just in case)
                'Y/m/d',  // YYYY/MM/DD
            ];

            foreach ($formats as $format) {
                try {
                    $parsedDate = \Carbon\Carbon::createFromFormat($format, $date);
                    return $parsedDate->format('d-m-Y');
                } catch (\Carbon\Exceptions\InvalidFormatException $e) {
                    // Continue to next format
                    continue;
                }
            }

            // If none of the specific formats work, fall back to the original parsing
            $parsedDate = \Carbon\Carbon::parse($date);
            return $parsedDate->format('d-m-Y');
        } catch (Exception $e) {
            // If parsing fails, return the original string
            return $date;
        }
    }


    /**
     * Clean string fields
     */
    private function cleanString($value)
    {
        if ($value === null || $value === '' || $value === 'null') {
            return null;
        }

        // Handle arrays by converting to JSON string or joining elements
        if (is_array($value)) {
            // If it's a simple array of strings, join them
            if (array_reduce($value, function ($carry, $item) {
                return $carry && is_string($item);
            }, true)) {
                return trim(implode(', ', $value));
            }

            // Otherwise, convert to JSON string
            return trim(json_encode($value));
        }

        // Remove newline characters and trim
        $cleaned = trim((string)$value);
        $cleaned = str_replace(["\r\n", "\r", "\n"], ' ', $cleaned);

        return $cleaned;
    }
}
