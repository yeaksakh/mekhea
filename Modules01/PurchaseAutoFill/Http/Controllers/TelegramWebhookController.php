<?php

namespace Modules\PurchaseAutoFill\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Modules\PurchaseAutoFill\Entities\TelegramOcrData;
use Modules\PurchaseAutoFill\Services\PurchaseInvoiceOcrService;
use Exception;

class TelegramWebhookController extends Controller
{
    protected $ocrService;

    public function __construct(PurchaseInvoiceOcrService $ocrService)
    {
        $this->ocrService = $ocrService;
    }

    /**
     * Handle incoming webhook updates from Telegram
     */
public function handleWebhook(Request $request, $businessId)
{
    try {
        // Get the bot token for this specific business
        $botToken = $this->getBotTokenForBusiness($businessId);
        
        if (!$botToken) {
            \Log::error('No bot token found for business ID: ' . $businessId);
            return response()->json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $update = $request->all();
        \Log::info('Telegram update received for business ' . $businessId . ':', $update);

        // Handle DOCUMENTS (files)
        if (isset($update['message']['document'])) {
            $chatId = $update['message']['chat']['id'];
            $messageId = $update['message']['message_id'];

            \Log::info("Processing document from chat ID: {$chatId}, message ID: {$messageId}");

            $existingRecord = TelegramOcrData::where('telegram_message_id', $messageId)
                ->where('telegram_chat_id', $chatId)
                ->where('business_id', $businessId)
                ->first();

            if ($existingRecord) {
                \Log::info("Message {$messageId} already processed, skipping");
                return response()->json(['ok' => true, 'status' => 'duplicate']);
            }

            $this->sendMessage($chatId, "Thank you! I've received your invoice and I'm processing it. This might take a moment...", $botToken);
            $this->processDocument($update['message'], $businessId, $botToken);
        }
        // Handle PHOTOS
        elseif (isset($update['message']['photo'])) {
            $chatId = $update['message']['chat']['id'];
            $messageId = $update['message']['message_id'];

            \Log::info("Processing photo from chat ID: {$chatId}, message ID: {$messageId}");

            $existingRecord = TelegramOcrData::where('telegram_message_id', $messageId)
                ->where('telegram_chat_id', $chatId)
                ->where('business_id', $businessId)
                ->first();

            if ($existingRecord) {
                \Log::info("Message {$messageId} already processed, skipping");
                return response()->json(['ok' => true, 'status' => 'duplicate']);
            }

            $this->sendMessage($chatId, "Thank you! I've received your invoice and I'm processing it. This might take a moment...", $botToken);
            $this->processImage($update['message'], $businessId, $botToken);
        }
        // Handle text messages
        elseif (isset($update['message']['text'])) {
            $chatId = $update['message']['chat']['id'];
            $text = $update['message']['text'];
            \Log::info("Processing text '{$text}' from chat ID: {$chatId}");

            $this->sendMessage(
                $chatId,
                "You said: '{$text}'.\n\n" .
                    "📸 Send me an invoice image (as photo or document)\n" .
                    "💡 Tip: Sending as a document (file) is more reliable!",
                $botToken
            );
        }

        // Single return point for successful processing
        return response()->json(['ok' => true]);

    } catch (\Exception $e) {
        \Log::error('Error processing Telegram webhook: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        // Still return 200 so Telegram doesn't retry
        // But log the error for debugging
        return response()->json(['ok' => false, 'error' => 'Internal error'], 200);
    }
}

    /**
     * Get bot token for specific business
     */
    private function getBotTokenForBusiness($businessId)
    {
        // You can get this from your database, config, or wherever you store business-specific tokens
        // Example: assuming you have a table that stores business tokens
        $business = \DB::table('purchaseautofill_socials') // or your actual table name
            ->where('business_id', $businessId)
            ->where('social_status', 1)
            ->first();

        return $business ? $business->social_token : null;
    }

    /**
     * Send a message to a Telegram chat
     */
    private function sendMessage($chatId, $message, $botToken)
    {
        $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
        ]);

        if (!$response->successful()) {
            \Log::error('Failed to send Telegram message: ' . $response->body());
        }

        return $response;
    }

    /**
     * Process an image sent to the bot
     */
    protected function processImage($message, $businessId, $botToken)
    {
        try {
            $chatId = $message['chat']['id'];
            $messageId = $message['message_id'];

            $photos = $message['photo'];
            $photo = $photos[count($photos) - 1];
            $fileId = $photo['file_id'];
            $fileUniqueId = $photo['file_unique_id'];

            $existingFile = TelegramOcrData::where('telegram_file_unique_id', $fileUniqueId)
                ->where('business_id', $businessId)
                ->first();
        
            if ($existingFile) {
                \Log::warning('User attempting to reuse an old photo', [
                    'messageId' => $messageId,
                    'fileUniqueId' => $fileUniqueId
                ]);
                
                $this->sendMessage($chatId, "⚠️ This photo was already processed.", $botToken);
                return;
            }

            \Log::info('Attempting to download file', [
                'messageId' => $messageId,
                'fileId' => $fileId,
                'fileUniqueId' => $fileUniqueId,
                'fileSize' => $photo['file_size'] ?? 0
            ]);

            $fileInfoUrl = "https://api.telegram.org/bot{$botToken}/getFile";
            $fileInfoResponse = Http::timeout(30)->post($fileInfoUrl, [
                'file_id' => $fileId
            ]);

            if (!$fileInfoResponse->successful()) {
                \Log::error('Telegram API request failed', [
                    'messageId' => $messageId,
                    'fileId' => $fileId,
                    'status' => $fileInfoResponse->status(),
                    'body' => $fileInfoResponse->body()
                ]);
                $this->sendMessage($chatId, "Sorry, I couldn't access your image. Please try uploading a new photo (don't forward or resend an old one).", $botToken);
                return;
            }

            $responseData = $fileInfoResponse->json();

            if (!isset($responseData['ok']) || !$responseData['ok']) {
                \Log::error('Telegram API returned error', [
                    'messageId' => $messageId,
                    'fileId' => $fileId,
                    'response' => $responseData
                ]);

                $errorMsg = $responseData['description'] ?? 'Unknown error';

                if (
                    strpos($errorMsg, 'wrong file_id') !== false ||
                    strpos($errorMsg, 'temporarily unavailable') !== false
                ) {
                    $this->sendMessage(
                        $chatId,
                        "⚠️ This image cannot be processed (file has expired).\n\n" .
                            "Please send a FRESH photo:\n" .
                            "1️⃣ Tap the 📎 attachment icon\n" .
                            "2️⃣ Choose your invoice image\n" .
                            "3️⃣ Send it\n\n" .
                            "❌ Don't use 'Resend' or 'Forward'\n" .
                            "✅ Upload the image fresh from your gallery",
                        $botToken
                    );
                } else {
                    $this->sendMessage($chatId, "Error: {$errorMsg}", $botToken);
                }
                return;
            }

            if (!isset($responseData['result']['file_path'])) {
                \Log::error('No file_path in response', [
                    'messageId' => $messageId,
                    'fileId' => $fileId,
                    'response' => $responseData
                ]);
                $this->sendMessage($chatId, "Sorry, I couldn't locate your image file.", $botToken);
                return;
            }

            $filePath = $responseData['result']['file_path'];
            $imageUrl = "https://api.telegram.org/file/bot{$botToken}/{$filePath}";

            \Log::info('Downloading image', [
                'messageId' => $messageId,
                'filePath' => $filePath
            ]);

            $imageResponse = Http::timeout(60)->get($imageUrl);

            if (!$imageResponse->successful()) {
                \Log::error('Failed to download image', [
                    'messageId' => $messageId,
                    'fileId' => $fileId,
                    'status' => $imageResponse->status()
                ]);
                $this->sendMessage($chatId, "Sorry, I couldn't download your image. Please try again.", $botToken);
                return;
            }

            $extension = pathinfo($filePath, PATHINFO_EXTENSION) ?: 'jpg';
            $filename = 'telegram_' . $messageId . '_' . time() . '.' . $extension;

            $publicDir = public_path('telegram_images');
            if (!is_dir($publicDir)) {
                mkdir($publicDir, 0755, true);
            }

            $fullPath = $publicDir . '/' . $filename;
            file_put_contents($fullPath, $imageResponse->body());

            $storedPath = 'telegram_images/' . $filename;

            \Log::info('Image stored successfully', [
                'messageId' => $messageId,
                'path' => $storedPath,
                'fullPath' => $fullPath,
                'size' => strlen($imageResponse->body())
            ]);

            $ocrData = TelegramOcrData::create([
                'business_id' => $businessId, // Now using dynamic business_id
                'telegram_file_id' => $fileId,
                'telegram_file_unique_id' => $photo['file_unique_id'],
                'telegram_file_size' => $photo['file_size'] ?? 0,
                'telegram_width' => $photo['width'] ?? 0,
                'telegram_height' => $photo['height'] ?? 0,
                'telegram_from' => $message['from']['first_name'] . ' ' . ($message['from']['last_name'] ?? ''),
                'telegram_date' => date('Y-m-d H:i:s', $message['date']),
                'telegram_message_id' => $messageId,
                'telegram_chat_id' => $chatId,
                'image_path' => $storedPath,
                'ocr_status' => 'processing',
                'status' => 'pending'
            ]);

            \Log::info('Database record created', [
                'id' => $ocrData->id,
                'messageId' => $messageId
            ]);

            $this->processWithOcr($ocrData, $chatId, $botToken);
        } catch (Exception $e) {
            \Log::error('Error in processImage: ' . $e->getMessage(), [
                'messageId' => $messageId ?? 'unknown',
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            if (isset($chatId)) {
                $this->sendMessage($chatId, "Sorry, something went wrong while processing your image. Please try uploading a new photo.", $botToken);
            }
        }
    }

    /**
     * Process the stored image with the OCR service (Gemini AI)
     */
    protected function processWithOcr($ocrData, $chatId, $botToken)
    {
        try {
            $imagePath = public_path($ocrData->image_path);

            if (!file_exists($imagePath)) {
                throw new Exception("Image file not found at: {$imagePath}");
            }

            $imageContent = file_get_contents($imagePath);
            if ($imageContent === false) {
                throw new Exception("Failed to read image file at: {$imagePath}");
            }

            $base64Image = base64_encode($imageContent);

            \Log::info('Processing OCR for image', [
                'ocr_id' => $ocrData->id,
                'image_path' => $imagePath,
                'image_size' => strlen($imageContent)
            ]);

            $ocrResult = $this->ocrService->extractInvoiceData($base64Image);

            if ($ocrResult['success']) {
                $updateData = [
                    'ocr_data' => $ocrResult['data'],
                    'ocr_status' => 'completed'
                ];

                $ocrFields = [
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
                    'final_total',
                    'advance_balance'
                ];

                foreach ($ocrFields as $field) {
                    if (isset($ocrResult['data'][$field])) {
                        $updateData[$field] = $ocrResult['data'][$field];
                    }
                }

                $ocrData->update($updateData);

                \Log::info('OCR processing completed successfully', [
                    'ocr_id' => $ocrData->id
                ]);

                $message = "✅ I've successfully processed your invoice!\n\n";
                if (isset($ocrResult['data']['supplier_name'])) {
                    $message .= "Supplier: " . $ocrResult['data']['supplier_name'] . "\n";
                }
                if (isset($ocrResult['data']['ref_no'])) {
                    $message .= "Reference: " . $ocrResult['data']['ref_no'] . "\n";
                }
                if (isset($ocrResult['data']['final_total'])) {
                    $message .= "Total: " . $ocrResult['data']['final_total'] . "\n";
                }
                $message .= "\nThe data has been saved to your system.";

                $this->sendMessage($chatId, $message, $botToken);
            } else {
                $ocrData->update([
                    'ocr_status' => 'failed',
                    'ocr_error' => $ocrResult['message'] ?? 'Unknown OCR error'
                ]);

                \Log::warning('OCR processing failed', [
                    'ocr_id' => $ocrData->id,
                    'error' => $ocrResult['message'] ?? 'Unknown OCR error'
                ]);

                $this->sendMessage($chatId, "❌ I couldn't extract the invoice data from your image. The image has been saved for manual processing.", $botToken);
            }
        } catch (Exception $e) {
            $ocrData->update([
                'ocr_status' => 'failed',
                'ocr_error' => $e->getMessage()
            ]);

            \Log::error('OCR processing failed: ' . $e->getMessage(), [
                'ocr_id' => $ocrData->id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->sendMessage($chatId, "❌ Something went wrong while processing your invoice. The image has been saved for manual processing.", $botToken);
        }
    }

    /**
     * Process document method (you'll need to implement this similar to processImage)
     */
    protected function processDocument($message, $businessId, $botToken)
    {
        // Similar implementation to processImage but for documents
        // You'll need to adapt this based on your needs
    }
}