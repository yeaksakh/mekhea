<?php

namespace Modules\ExpenseAutoFill\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\ExpenseAutoFill\Entities\TelegramExpenseImageData;
use Exception;

class TelegramExpenseImageWebhookController extends Controller
{
    protected $botToken;

    public function __construct()
    {
        $this->botToken = '8528115811:AAFJrbYLZeyNyEJk9-Z34yOqSSQTXUii0BQ';
    }

    /**
     * Handle incoming webhook updates from Telegram
     */
    public function handleWebhook(Request $request)
    {
        try {
            $update = $request->all();
            $message = $update['message'] ?? null;
            
            if (!$message) {
                return response('OK', 200);
            }

            $chatId = $message['chat']['id'];
            $messageId = $message['message_id'];

            // Check if we've already processed this message
            if ($this->isMessageProcessed($messageId, $chatId)) {
                return response('OK', 200);
            }

            // Process based on message type
            if (isset($message['document'])) {
                $this->processDocument($message);
            } elseif (isset($message['photo'])) {
                $this->processPhoto($message);
            }

            return response('OK', 200);
        } catch (Exception $e) {
            \Log::error('Error processing Telegram webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('OK', 200); // Always return 200 to Telegram
        }
    }

    /**
     * Process a document sent to the bot
     */
    protected function processDocument(array $message): void
    {
        $document = $message['document'];
        $fileId = $document['file_id'];
        $messageId = $message['message_id'];
        $chatId = $message['chat']['id'];

        // Get file info
        $fileInfo = $this->getFileInfo($fileId);
        if (!$fileInfo) {
            return;
        }

        // Download file
        $fileContent = $this->downloadFile($fileInfo['file_path']);
        if (!$fileContent) {
            return;
        }

        // Store file using the approach from the snippet
        $extension = pathinfo($document['file_name'], PATHINFO_EXTENSION);
        $filename = "telegram_doc_{$messageId}_" . time() . ".{$extension}";
        $storedPath = $this->storeFile($fileContent, 'telegram_documents', $filename);
        
        if (!$storedPath) {
            return;
        }

        // Save to database
        $this->saveImageData([
            'telegram_file_id' => $fileId,
            'telegram_file_unique_id' => $document['file_unique_id'],
            'telegram_file_size' => $document['file_size'] ?? 0,
            'telegram_file_name' => $document['file_name'],
            'file_path' => $storedPath,
            'message' => $message
        ]);
    }

    /**
     * Process a photo sent to the bot
     */
    protected function processPhoto(array $message): void
    {
        $photos = $message['photo'];
        $photo = end($photos); // Get highest resolution
        $fileId = $photo['file_id'];
        $messageId = $message['message_id'];

        // Get file info
        $fileInfo = $this->getFileInfo($fileId);
        if (!$fileInfo) {
            return;
        }

        // Download file
        $fileContent = $this->downloadFile($fileInfo['file_path']);
        if (!$fileContent) {
            return;
        }

        // Store file using the approach from the snippet
        $extension = pathinfo($fileInfo['file_path'], PATHINFO_EXTENSION) ?: 'jpg';
        $filename = "telegram_{$messageId}_" . time() . ".{$extension}";
        $storedPath = $this->storeFile($fileContent, 'telegram_images', $filename);
        
        if (!$storedPath) {
            return;
        }

        // Save to database
        $this->saveImageData([
            'telegram_file_id' => $fileId,
            'telegram_file_unique_id' => $photo['file_unique_id'],
            'telegram_file_size' => $photo['file_size'] ?? 0,
            'telegram_width' => $photo['width'] ?? 0,
            'telegram_height' => $photo['height'] ?? 0,
            'file_path' => $storedPath,
            'message' => $message
        ]);
    }

    /**
     * Save image data to database
     */
    protected function saveImageData(array $data): void
    {
        $message = $data['message'];
        $user = $message['from'];
        
        TelegramExpenseImageData::create([
            'business_id' => $this->getBusinessId(),
            'telegram_file_id' => $data['telegram_file_id'],
            'telegram_file_unique_id' => $data['telegram_file_unique_id'],
            'telegram_file_size' => $data['telegram_file_size'],
            'telegram_file_name' => $data['telegram_file_name'] ?? null,
            'telegram_width' => $data['telegram_width'] ?? null,
            'telegram_height' => $data['telegram_height'] ?? null,
            'telegram_user_id' => $user['id'],
            'telegram_user_first_name' => $user['first_name'] ?? '',
            'telegram_user_last_name' => $user['last_name'] ?? '',
            'telegram_user_username' => $user['username'] ?? '',
            'telegram_user_photo_url' => $this->getUserProfilePhoto($user['id']),
            'telegram_date' => date('Y-m-d H:i:s', $message['date']),
            'telegram_message_id' => $message['message_id'],
            'telegram_chat_id' => $message['chat']['id'],
            'file_path' => $data['file_path'],
            'status' => 'stored'
        ]);
    }

    /**
     * Get business ID - should be implemented based on your application logic
     */
    protected function getBusinessId(): int
    {
        // This should be implemented based on your application logic
        // For example, you might get it from the authenticated user or from the chat
        return 3; // Default value
    }

    /**
     * Check if message already processed
     */
    protected function isMessageProcessed(int $messageId, int $chatId): bool
    {
        return TelegramExpenseImageData::where('telegram_message_id', $messageId)
            ->where('telegram_chat_id', $chatId)
            ->exists();
    }

    /**
     * Get file information from Telegram
     */
    protected function getFileInfo(string $fileId): ?array
    {
        try {
            $response = Http::timeout(30)->post("https://api.telegram.org/bot{$this->botToken}/getFile", [
                'file_id' => $fileId
            ]);

            if (!$response->successful() || !$response->json('ok')) {
                \Log::error('Failed to get file info', [
                    'fileId' => $fileId,
                    'response' => $response->body()
                ]);
                return null;
            }

            return $response->json('result');
        } catch (\Exception $e) {
            \Log::error('Exception while getting file info', [
                'fileId' => $fileId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Download file from Telegram
     */
    protected function downloadFile(string $filePath): ?string
    {
        try {
            $fileUrl = "https://api.telegram.org/file/bot{$this->botToken}/{$filePath}";
            $response = Http::timeout(60)->get($fileUrl);

            if (!$response->successful()) {
                \Log::error('Failed to download file', [
                    'filePath' => $filePath,
                    'status' => $response->status()
                ]);
                return null;
            }

            return $response->body();
        } catch (\Exception $e) {
            \Log::error('Exception while downloading file', [
                'filePath' => $filePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get user profile photo URL
     */
    protected function getUserProfilePhoto(int $userId): string
    {
        $cacheKey = "telegram_user_photo_{$userId}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        $defaultAvatar = 'https://ui-avatars.com/api/?name=User&background=0088cc&color=fff';
        
        try {
            $response = Http::get("https://api.telegram.org/bot{$this->botToken}/getUserProfilePhotos", [
                'user_id' => $userId,
                'limit' => 1
            ]);
            
            if (!$response->successful() || !$response->json('ok') || empty($response->json('result.photos'))) {
                return $defaultAvatar;
            }
            
            $photos = $response->json('result.photos');
            $photo = end($photos[0]);
            $fileId = $photo['file_id'];
            
            $fileInfo = $this->getFileInfo($fileId);
            if (!$fileInfo) {
                return $defaultAvatar;
            }
            
            $photoUrl = "https://api.telegram.org/file/bot{$this->botToken}/{$fileInfo['file_path']}";
            
            Cache::put($cacheKey, $photoUrl, now()->addHours(24));
            
            return $photoUrl;
        } catch (\Exception $e) {
            \Log::error('Exception getting user profile photo', [
                'userId' => $userId,
                'error' => $e->getMessage()
            ]);
            return $defaultAvatar;
        }
    }

    /**
     * Store file to storage using the approach from the snippet
     */
    protected function storeFile(string $fileContent, string $directory, string $filename): ?string
    {
        try {
            // Create directory if it doesn't exist
            $publicDir = public_path($directory);
            if (!is_dir($publicDir)) {
                mkdir($publicDir, 0755, true);
            }

            // Store file directly in public directory
            $fullPath = $publicDir . '/' . $filename;
            file_put_contents($fullPath, $fileContent);

            // Store the relative path for database record
            $storedPath = $directory . '/' . $filename;
            
            return $storedPath;
        } catch (\Exception $e) {
            \Log::error('Exception storing file', [
                'directory' => $directory,
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}