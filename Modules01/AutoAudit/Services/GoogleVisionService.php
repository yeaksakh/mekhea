<?php

namespace Modules\AutoAudit\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GoogleVisionService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = 'AIzaSyBypasDe2PVoMoZre9FtV4P0-RazYdwttU';
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    }

    /**
     * Extract structured payment data - cleaner response format
     */
    public function extractPaymentData($imageContent)
    {
        $prompt = $this->buildPaymentExtractionPrompt();

        try {
            // Validate inputs first
            if (empty($this->apiKey)) {
                throw new Exception('Google Vision API key is not configured');
            }

            if (empty($imageContent)) {
                throw new Exception('No image content provided');
            }

            Log::info('Extracting payment data from image', [
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
            Log::error('Payment data extraction failed', [
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
     * Build specific prompt for payment data extraction
     */
    private function buildPaymentExtractionPrompt()
    {
        return "Please analyze this payment screenshot and extract the payment information. Return your response as a JSON object with these fields:

            {
                \"amount\": \"the transaction amount as a number\",
                \"currency\": \"currency code (USD, KHR, etc.)\",
                \"date\": \"transaction date\",
                \"time\": \"transaction time\",
                \"reference\": \"transaction reference number or ID\",
                \"sender\": \"sender/payer name\",
                \"receiver\": \"receiver/payee name\",
                \"bank\": \"bank name\",
                \"account\": \"account number\",
                \"type\": \"transaction type (transfer, payment, etc.)\",
                \"description\": \"transaction description if any\"
            }

            Important rules:
            - Extract the exact amount as shown (include negative sign if it's a debit/outgoing payment)
            - Use the currency code shown in the image
            - If any information is not visible, use null
            - Return ONLY the JSON object, no extra text or formatting
            - Make sure the JSON is properly formatted and valid";
                }

    /**
     * Parse AI response and clean it up
     */
    private function parseAndCleanResponse($aiResponse)
    {
        try {
            // Clean the response to get pure JSON
            $cleanJson = $this->extractJsonFromResponse($aiResponse);

            Log::info('Cleaned JSON extracted', [
                'original_length' => strlen($aiResponse),
                'cleaned_length' => strlen($cleanJson)
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
                    'message' => 'Invalid JSON response from AI',
                    'data' => null,
                    'raw_response' => $aiResponse
                ];
            }

            // Clean and validate the data
            $cleanedData = $this->cleanExtractedData($parsedData);

            return [
                'success' => true,
                'message' => 'Payment data extracted successfully',
                'data' => $cleanedData,
                'raw_response' => $aiResponse // Include for debugging
            ];
        } catch (Exception $e) {
            Log::error('Response parsing failed', [
                'error' => $e->getMessage(),
                'response' => $aiResponse
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

        return trim($response);
    }

    /**
     * Clean and validate extracted data
     */
    private function cleanExtractedData($data)
    {
        $cleaned = [];

        // Clean amount
        $cleaned['amount'] = $this->cleanAmount($data['amount'] ?? null);

        // Clean currency
        $cleaned['currency'] = $this->cleanCurrency($data['currency'] ?? null);

        // Clean date and time
        $cleaned['date'] = $this->cleanString($data['date'] ?? null);
        $cleaned['time'] = $this->cleanString($data['time'] ?? null);
        $cleaned['datetime'] = $this->combineDateTime($cleaned['date'], $cleaned['time']);

        // Clean text fields
        $cleaned['reference'] = $this->cleanString($data['reference'] ?? null);
        $cleaned['sender'] = $this->cleanString($data['sender'] ?? null);
        $cleaned['receiver'] = $this->cleanString($data['receiver'] ?? null);
        $cleaned['bank'] = $this->cleanString($data['bank'] ?? null);
        $cleaned['account'] = $this->cleanString($data['account'] ?? null);
        $cleaned['type'] = $this->cleanString($data['type'] ?? null);
        $cleaned['description'] = $this->cleanString($data['description'] ?? null);

        return $cleaned;
    }

    /**
     * Clean amount field
     */
    private function cleanAmount($amount)
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        // Remove any non-numeric characters except decimal point and negative sign
        $cleanAmount = preg_replace('/[^\d.-]/', '', $amount);

        // Convert to float
        return floatval($cleanAmount);
    }

    /**
     * Clean currency field
     */
    private function cleanCurrency($currency)
    {
        if (empty($currency)) {
            return null;
        }

        // Convert to uppercase and remove extra spaces
        $currency = strtoupper(trim($currency));

        // Extract currency code (usually 3 letters)
        if (preg_match('/([A-Z]{3})/', $currency, $matches)) {
            return $matches[1];
        }

        return $currency;
    }

    /**
     * Clean string fields
     */
    private function cleanString($value)
    {
        if ($value === null || $value === '' || $value === 'null') {
            return null;
        }

        return trim($value);
    }

    /**
     * Combine date and time into a single datetime string
     */
    private function combineDateTime($date, $time)
    {
        if (empty($date)) {
            return null;
        }

        $dateTime = $date;
        if (!empty($time)) {
            $dateTime .= ' ' . $time;
        }

        // Try to parse and format the datetime
        try {
            $parsedDate = \Carbon\Carbon::parse($dateTime);
            return $parsedDate->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            // If parsing fails, return the original combined string
            return $dateTime;
        }
    }

    /**
     * Legacy method for backward compatibility
     */
    public function extractText($imageContent)
    {
        try {
            // Validate inputs
            if (empty($this->apiKey)) {
                throw new Exception('Google Vision API key is not configured');
            }

            if (empty($imageContent)) {
                throw new Exception('No image content provided');
            }

            Log::info('Extracting text from image', [
                'content_length' => strlen($imageContent)
            ]);

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
                                'text' => 'Extract all text from this image and format it clearly. if there is circle red line extract all text inside that circle image.'
                            ]
                        ]
                    ]
                ]
            ]);

            if (!$response->successful()) {
                throw new Exception('API request failed: ' . $response->body());
            }

            $data = $response->json();

            if (empty($data['candidates'][0]['content']['parts'][0]['text'])) {
                return [
                    'success' => true,
                    'text' => 'No text extracted'
                ];
            }

            return [
                'success' => true,
                'text' => $data['candidates'][0]['content']['parts'][0]['text']
            ];
        } catch (Exception $e) {
            Log::error('Text extraction failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
