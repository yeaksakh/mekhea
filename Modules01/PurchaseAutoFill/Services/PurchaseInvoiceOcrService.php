<?php

namespace Modules\PurchaseAutoFill\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Storage;

class PurchaseInvoiceOcrService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = 'AIzaSyBypasDe2PVoMoZre9FtV4P0-RazYdwttU';
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    }

    /**
     * Extract structured purchase invoice data from image
     */
    public function extractInvoiceData($imageContent)
    {
        $prompt = $this->buildInvoiceExtractionPrompt();

        try {
            // Validate inputs first
            if (empty($this->apiKey)) {
                throw new Exception('Google Vision API key is not configured');
            }

            if (empty($imageContent)) {
                throw new Exception('No image content provided');
            }

            Log::info('Extracting invoice data from image', [
                'content_length' => strlen($imageContent)
            ]);

            // Make API request
            $response = Http::timeout(30)->withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $this->apiKey
            ])->post($this->apiUrl, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'inlineData' => [
                                    'mimeType' => 'image/jpeg',
                                    'data' => $imageContent
                                ]
                            ],
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ]
            ]);

            if (!$response->successful()) {
                Log::error('Gemini API request failed', [
                    'status' => $response->status(),
                    'error' => $response->body()
                ]);
                throw new Exception('API request failed: ' . $response->body());
            }

            $data = $response->json();


            if (empty($data['candidates'][0]['content']['parts'][0]['text'])) {
                throw new Exception('No response from AI');
            }

            $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'];

            Log::info('AI response received', [
                'response_length' => strlen($aiResponse)
            ]);

            // Parse the response into clean structure
            return $this->parseAndCleanResponse($aiResponse);
        } catch (Exception $e) {
            Log::error('Invoice data extraction failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Build specific prompt for invoice data extraction
     */

    private function buildInvoiceExtractionPrompt()
    {
        return <<<PROMPT
You are an intelligent financial document parser specializing in Invoices, Delivery Notes, and Receipts.
Your task is to analyze the image and extract structured data for an accounting system.

### OUR COMPANY NAMES (the recipient / "Bill To" / "Ship To"):
- Mayako
- The Foxest Art
- ម៉ាយាកុ
- ឌឹ ហ្វក់សេស អាត ឯ.ក
Any variation or abbreviation of the above is OUR company.

### CRITICAL EXTRACTION RULES:

1. **SUPPLIER (contact_id & supplier_name)**  
   - Supplier = entity SELLING / SENDING the goods (usually top header/logo).  
   - NEVER use our company names above as supplier.  
   - Prefer Khmer script name when present; include English if available.

2. **OUR COMPANY (company_name)**  
   - Look for "Bill To", "Ship To", "Customer", "Deliver To".  
   - Match any of our names listed above (case/accent insensitive).  
   - Put the exact text found into "company_name" (keep Khmer if that's what's written).

3. **LINE ITEMS (product)**  
   - Extract every item with quantity + unit.  
   - 'product' must be a valid JSON string of array of objects:  
     [{"name": "exact item name (Khmer or English)", "quantity": 5000, "unit": "PCS", "price": null, "subtotal": null}]  
   - Keep original Khmer product names when present.

4. **DOCUMENT TYPE** → set "document" to "Invoice", "Delivery Note", or "Receipt".

5. **DATES** → always YYYY-MM-DD.

### OUTPUT ONLY THIS EXACT JSON (no extra text, no markdown):

{
  "contact_id": null,
  "company_name": null,
  "ref_no": null,
  "transaction_date": null,
  "status": null,
  "location_id": null,
  "exchange_rate": null,
  "pay_term_number": null,
  "pay_term_type": null,
  "document": null,
  "custom_field_1": null,
  "custom_field_2": null,
  "custom_field_3": null,
  "custom_field_4": null,
  "purchase_order_ids": null,
  "product": null,
  "discount_type": null,
  "discount_amount": null,
  "tax_id": null,
  "tax_amount": null,
  "additional_notes": null,
  "shipping_details": null,
  "shipping_charges": null,
  "shipping_custom_field_1": null,
  "shipping_custom_field_2": null,
  "shipping_custom_field_3": null,
  "shipping_custom_field_4": null,
  "shipping_custom_field_5": null,
  "additional_expense_key_1": null,
  "additional_expense_value_1": null,
  "additional_expense_key_2": null,
  "additional_expense_value_2": null,
  "additional_expense_key_3": null,
  "additional_expense_value_3": null,
  "additional_expense_key_4": null,
  "additional_expense_value_4": null,
  "final_total": null,
  "advance_balance": null,
  "supplier_name": null
}

Rules:
- Return ONLY valid JSON.
- Use null for missing values.
- Never confuse supplier with our company names.
- Keep Khmer text exactly as it appears.

Extract now.
PROMPT;
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
    private function cleanExtractedData($data)
    {
        $cleaned = [];

        // Define all expected fields with default null values
        $expectedFields = [
            'contact_id',
            'supplier_name',
            'company_name',
            'ref_no',
            'transaction_date',
            'status',
            'location_id',
            'exchange_rate',
            'pay_term_number',
            'pay_term_type',
            'document',
            'custom_field_1',
            'custom_field_2',
            'custom_field_3',
            'custom_field_4',
            'purchase_order_ids',
            'product',
            'discount_type',
            'discount_amount',
            'tax_id',
            'tax_amount',
            'additional_notes',
            'shipping_details',
            'shipping_charges',
            'shipping_custom_field_1',
            'shipping_custom_field_2',
            'shipping_custom_field_3',
            'shipping_custom_field_4',
            'shipping_custom_field_5',
            'additional_expense_key_1',
            'additional_expense_value_1',
            'additional_expense_key_2',
            'additional_expense_value_2',
            'additional_expense_key_3',
            'additional_expense_value_3',
            'additional_expense_key_4',
            'additional_expense_value_4',
            'final_total',
            'advance_balance'
        ];

        // Process each field
        foreach ($expectedFields as $field) {
            if (!isset($data[$field])) {
                $cleaned[$field] = null;
                continue;
            }

            // Apply specific cleaning based on field type
            switch ($field) {
                case 'product':
                    // Special handling for product field which is stored as JSON
                    if (is_array($data[$field])) {
                        // If it's already an array, clean each item
                        $cleaned[$field] = array_map(function ($item) {
                            return $this->cleanString($item);
                        }, $data[$field]);
                    }
                    // If it's a string, convert it to a single-item array
                    else {
                        $cleaned[$field] = [$this->cleanString($data[$field])];
                    }
                    break;

                case 'final_total':
                case 'discount_amount':
                case 'tax_amount':
                case 'shipping_charges':
                case 'exchange_rate':
                case 'advance_balance':
                case 'additional_expense_value_1':
                case 'additional_expense_value_2':
                case 'additional_expense_value_3':
                case 'additional_expense_value_4':
                    $cleaned[$field] = $this->cleanAmount($data[$field]);
                    break;

                case 'transaction_date':
                    $cleaned[$field] = $this->cleanDate($data[$field]);
                    break;

                case 'pay_term_number':
                    $cleaned[$field] = $this->cleanInteger($data[$field]);
                    break;

                default:
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

        // Remove any non-numeric characters except decimal point and negative sign
        $cleanAmount = preg_replace('/[^\d.-]/', '', $amount);

        // Convert to float
        return floatval($cleanAmount);
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
            $parsedDate = \Carbon\Carbon::parse($date);
            return $parsedDate->format('Y-m-d');
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
