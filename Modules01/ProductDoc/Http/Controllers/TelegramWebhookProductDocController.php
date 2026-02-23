<?php

namespace Modules\ProductDoc\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ProductDoc\Entities\ProductDocSocial;
use Modules\ProductDoc\Entities\ProductDocFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class TelegramWebhookProductDocController extends Controller
{
    /**
     * Handle incoming webhook from Telegram
     */
    public function handleWebhook(Request $request, $id)
    {
        // Get the update from Telegram
        $update = $request->all();
        
        // Log the update for debugging
        Log::info('Telegram Update:', $update);
        
        // Find the social media configuration
        $socialConfig = ProductDocSocial::where('business_id', $id)
            ->where('social_type', 'telegram')
            ->where('social_status', 1)
            ->first();
            
        if (!$socialConfig) {
            Log::error('Social configuration not found for ID: ' . $id);
            return response()->json(['status' => 'error', 'message' => 'Configuration not found']);
        }
        
        // Check if this is a message with video or document
        if (isset($update['message'])) {
            $message = $update['message'];
            
            // Handle video note
            if (isset($message['video_note'])) {
                $this->handleVideoNote($message['video_note'], $message, $socialConfig);
            }
            
            // Handle regular video
            if (isset($message['video'])) {
                $this->handleVideo($message['video'], $message, $socialConfig);
            }
            
            // Handle document
            if (isset($message['document'])) {
                $this->handleDocument($message['document'], $message, $socialConfig);
            }
            
            // Handle photo
            if (isset($message['photo'])) {
                $this->handlePhoto($message['photo'], $message, $socialConfig);
            }
        }
        
        return response()->json(['status' => 'success']);
    }
    
    /**
     * Handle video note messages
     */
    private function handleVideoNote($videoNote, $message, $socialConfig)
    {
        try {
            $chatId = $message['chat']['id'];
            $messageId = $message['message_id'];
            $businessId = $socialConfig->business_id;
            $botToken = $socialConfig->social_token;

            // Extract file information
            $fileId = $videoNote['file_id'];
            $fileUniqueId = $videoNote['file_unique_id'];
            $fileSize = $videoNote['file_size'] ?? null;
            $duration = $videoNote['duration'] ?? null;
            $length = $videoNote['length'] ?? null;

            // Check if file already exists
            $existingFile = ProductDocFile::where('file_unique_id', $fileUniqueId)
                ->where('business_id', $businessId)
                ->first();

            if ($existingFile) {
                Log::warning('User attempting to reuse an old video note', [
                    'messageId' => $messageId,
                    'fileUniqueId' => $fileUniqueId
                ]);
                
                $this->sendMessage($chatId, "⚠️ This video note was already processed.", $botToken);
                return;
            }

            // Download and store thumbnail if available
            $thumbnailPath = null;
            if (isset($videoNote['thumbnail'])) {
                $thumbnailPath = $this->downloadThumbnail(
                    $videoNote['thumbnail'], 
                    $messageId, 
                    $businessId, 
                    $botToken
                );
            }

            // Store the video note information
            $videoNoteData = ProductDocFile::create([
                'business_id' => $businessId,
                'social_id' => $socialConfig->id,
                'file_type' => 'video_note',
                'file_id' => $fileId,
                'file_unique_id' => $fileUniqueId,
                'file_size' => $fileSize,
                'duration' => $duration,
                'length' => $length,
                'thumbnail' => isset($videoNote['thumbnail']) ? json_encode($videoNote['thumbnail']) : null,
                'thumbnail_path' => $thumbnailPath,
                'from_user_id' => $message['from']['id'],
                'from_user_name' => ($message['from']['first_name'] ?? '') . ' ' . ($message['from']['last_name'] ?? ''),
                'from_user_username' => $message['from']['username'] ?? null,
                'message_id' => $messageId,
                'message_date' => date('Y-m-d H:i:s', $message['date']),
                'status' => 'pending'
            ]);

            Log::info('Video note record created', [
                'id' => $videoNoteData->id,
                'messageId' => $messageId
            ]);

            // Download the main video file if needed
            $localPath = $this->downloadFile($fileId, $socialConfig, 'video_notes');
            
            // Update the record with the local path
            if ($localPath) {
                $videoNoteData->local_path = $localPath;
                $videoNoteData->save();
            }

            // Send confirmation message
            $this->sendMessage($chatId, "✅ Video note received and processed.", $botToken);

        } catch (Exception $e) {
            Log::error('Error in handleVideoNote: ' . $e->getMessage(), [
                'messageId' => $messageId ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            if (isset($chatId)) {
                $this->sendMessage($chatId, "Sorry, something went wrong while processing your video note. Please try again.", $botToken);
            }
        }
    }
    
    /**
     * Handle regular video messages
     */
    private function handleVideo($video, $message, $socialConfig)
    {
        try {
            $chatId = $message['chat']['id'];
            $messageId = $message['message_id'];
            $businessId = $socialConfig->business_id;
            $botToken = $socialConfig->social_token;

            // Extract file information
            $fileId = $video['file_id'];
            $fileUniqueId = $video['file_unique_id'];
            $fileSize = $video['file_size'] ?? null;
            $duration = $video['duration'] ?? null;
            $width = $video['width'] ?? null;
            $height = $video['height'] ?? null;
            $fileName = $video['file_name'] ?? null;
            $mimeType = $video['mime_type'] ?? null;

            // Check if file already exists
            $existingFile = ProductDocFile::where('file_unique_id', $fileUniqueId)
                ->where('business_id', $businessId)
                ->first();

            if ($existingFile) {
                Log::warning('User attempting to reuse an old video', [
                    'messageId' => $messageId,
                    'fileUniqueId' => $fileUniqueId
                ]);
                
                $this->sendMessage($chatId, "⚠️ This video was already processed.", $botToken);
                return;
            }

            // Download and store thumbnail if available
            $thumbnailPath = null;
            if (isset($video['thumbnail'])) {
                $thumbnailPath = $this->downloadThumbnail(
                    $video['thumbnail'], 
                    $messageId, 
                    $businessId, 
                    $botToken
                );
            }

            // Store the video information
            $videoData = ProductDocFile::create([
                'business_id' => $businessId,
                'social_id' => $socialConfig->id,
                'file_type' => 'video',
                'file_id' => $fileId,
                'file_unique_id' => $fileUniqueId,
                'file_size' => $fileSize,
                'duration' => $duration,
                'width' => $width,
                'height' => $height,
                'file_name' => $fileName,
                'mime_type' => $mimeType,
                'thumbnail' => isset($video['thumbnail']) ? json_encode($video['thumbnail']) : null,
                'thumbnail_path' => $thumbnailPath,
                'from_user_id' => $message['from']['id'],
                'from_user_name' => ($message['from']['first_name'] ?? '') . ' ' . ($message['from']['last_name'] ?? ''),
                'from_user_username' => $message['from']['username'] ?? null,
                'message_id' => $messageId,
                'message_date' => date('Y-m-d H:i:s', $message['date']),
                'status' => 'pending'
            ]);

            Log::info('Video record created', [
                'id' => $videoData->id,
                'messageId' => $messageId
            ]);

            // Download the main video file if needed
            $localPath = $this->downloadFile($fileId, $socialConfig, 'videos');
            
            // Update the record with the local path
            if ($localPath) {
                $videoData->local_path = $localPath;
                $videoData->save();
            }

            // Send confirmation message
            $this->sendMessage($chatId, "✅ Video received and processed.", $botToken);

        } catch (Exception $e) {
            Log::error('Error in handleVideo: ' . $e->getMessage(), [
                'messageId' => $messageId ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            if (isset($chatId)) {
                $this->sendMessage($chatId, "Sorry, something went wrong while processing your video. Please try again.", $botToken);
            }
        }
    }
    
    /**
     * Handle document messages
     */
    private function handleDocument($document, $message, $socialConfig)
    {
        try {
            $chatId = $message['chat']['id'];
            $messageId = $message['message_id'];
            $businessId = $socialConfig->business_id;
            $botToken = $socialConfig->social_token;

            // Extract file information
            $fileId = $document['file_id'];
            $fileUniqueId = $document['file_unique_id'];
            $fileSize = $document['file_size'] ?? null;
            $fileName = $document['file_name'] ?? null;
            $mimeType = $document['mime_type'] ?? null;

            // Check if file already exists
            $existingFile = ProductDocFile::where('file_unique_id', $fileUniqueId)
                ->where('business_id', $businessId)
                ->first();

            if ($existingFile) {
                Log::warning('User attempting to reuse an old document', [
                    'messageId' => $messageId,
                    'fileUniqueId' => $fileUniqueId
                ]);
                
                $this->sendMessage($chatId, "⚠️ This document was already processed.", $botToken);
                return;
            }

            // Download and store thumbnail if available
            $thumbnailPath = null;
            if (isset($document['thumbnail'])) {
                $thumbnailPath = $this->downloadThumbnail(
                    $document['thumbnail'], 
                    $messageId, 
                    $businessId, 
                    $botToken
                );
            }

            // Store the document information
            $documentData = ProductDocFile::create([
                'business_id' => $businessId,
                'social_id' => $socialConfig->id,
                'file_type' => 'document',
                'file_id' => $fileId,
                'file_unique_id' => $fileUniqueId,
                'file_size' => $fileSize,
                'file_name' => $fileName,
                'mime_type' => $mimeType,
                'thumbnail' => isset($document['thumbnail']) ? json_encode($document['thumbnail']) : null,
                'thumbnail_path' => $thumbnailPath,
                'from_user_id' => $message['from']['id'],
                'from_user_name' => ($message['from']['first_name'] ?? '') . ' ' . ($message['from']['last_name'] ?? ''),
                'from_user_username' => $message['from']['username'] ?? null,
                'message_id' => $messageId,
                'message_date' => date('Y-m-d H:i:s', $message['date']),
                'status' => 'pending'
            ]);

            Log::info('Document record created', [
                'id' => $documentData->id,
                'messageId' => $messageId
            ]);

            // Download the main document file if needed
            $localPath = $this->downloadFile($fileId, $socialConfig, 'documents');
            
            // Update the record with the local path
            if ($localPath) {
                $documentData->local_path = $localPath;
                $documentData->save();
            }

            // Send confirmation message
            $this->sendMessage($chatId, "✅ Document received and processed.", $botToken);

        } catch (Exception $e) {
            Log::error('Error in handleDocument: ' . $e->getMessage(), [
                'messageId' => $messageId ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            if (isset($chatId)) {
                $this->sendMessage($chatId, "Sorry, something went wrong while processing your document. Please try again.", $botToken);
            }
        }
    }
    
    /**
     * Handle photo messages
     */
    private function handlePhoto($photos, $message, $socialConfig)
    {
        try {
            $chatId = $message['chat']['id'];
            $messageId = $message['message_id'];
            $businessId = $socialConfig->business_id;
            $botToken = $socialConfig->social_token;

            // Get the highest resolution photo
            $photo = $photos[count($photos) - 1];
            $fileId = $photo['file_id'];
            $fileUniqueId = $photo['file_unique_id'];

            // Check if file already exists
            $existingFile = ProductDocFile::where('file_unique_id', $fileUniqueId)
                ->where('business_id', $businessId)
                ->first();

            if ($existingFile) {
                Log::warning('User attempting to reuse an old photo', [
                    'messageId' => $messageId,
                    'fileUniqueId' => $fileUniqueId
                ]);
                
                $this->sendMessage($chatId, "⚠️ This photo was already processed.", $botToken);
                return;
            }

            // Store the photo information
            $photoData = ProductDocFile::create([
                'business_id' => $businessId,
                'social_id' => $socialConfig->id,
                'file_type' => 'photo',
                'file_id' => $fileId,
                'file_unique_id' => $fileUniqueId,
                'file_size' => $photo['file_size'] ?? null,
                'width' => $photo['width'] ?? null,
                'height' => $photo['height'] ?? null,
                'from_user_id' => $message['from']['id'],
                'from_user_name' => ($message['from']['first_name'] ?? '') . ' ' . ($message['from']['last_name'] ?? ''),
                'from_user_username' => $message['from']['username'] ?? null,
                'message_id' => $messageId,
                'message_date' => date('Y-m-d H:i:s', $message['date']),
                'status' => 'pending'
            ]);

            Log::info('Photo record created', [
                'id' => $photoData->id,
                'messageId' => $messageId
            ]);

            // Download the photo file
            $localPath = $this->downloadFile($fileId, $socialConfig, 'photos');
            
            // Update the record with the local path
            if ($localPath) {
                $photoData->local_path = $localPath;
                $photoData->save();
            }

            // Send confirmation message
            $this->sendMessage($chatId, "✅ Photo received and processed.", $botToken);

        } catch (Exception $e) {
            Log::error('Error in handlePhoto: ' . $e->getMessage(), [
                'messageId' => $messageId ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            if (isset($chatId)) {
                $this->sendMessage($chatId, "Sorry, something went wrong while processing your photo. Please try again.", $botToken);
            }
        }
    }
    
    /**
     * Download and store thumbnail from Telegram
     *
     * @param array $thumbnailData The thumbnail data from Telegram
     * @param int $messageId The message ID
     * @param int $businessId The business ID
     * @param string $botToken The bot token
     * @return string|null The path to the stored thumbnail or null if failed
     */
    protected function downloadThumbnail($thumbnailData, $messageId, $businessId, $botToken)
    {
        try {
            if (!isset($thumbnailData['file_id'])) {
                Log::warning('No file_id in thumbnail data', [
                    'messageId' => $messageId
                ]);
                return null;
            }

            $fileId = $thumbnailData['file_id'];
            $fileUniqueId = $thumbnailData['file_unique_id'];

            Log::info('Attempting to download thumbnail', [
                'messageId' => $messageId,
                'fileId' => $fileId,
                'fileUniqueId' => $fileUniqueId
            ]);

            // Get file info from Telegram
            $fileInfoUrl = "https://api.telegram.org/bot{$botToken}/getFile";
            $fileInfoResponse = Http::timeout(30)->post($fileInfoUrl, [
                'file_id' => $fileId
            ]);

            if (!$fileInfoResponse->successful()) {
                Log::error('Failed to get thumbnail file info', [
                    'messageId' => $messageId,
                    'fileId' => $fileId,
                    'status' => $fileInfoResponse->status(),
                    'body' => $fileInfoResponse->body()
                ]);
                return null;
            }

            $responseData = $fileInfoResponse->json();

            if (!isset($responseData['ok']) || !$responseData['ok'] || !isset($responseData['result']['file_path'])) {
                Log::error('Invalid response for thumbnail file info', [
                    'messageId' => $messageId,
                    'fileId' => $fileId,
                    'response' => $responseData
                ]);
                return null;
            }

            $filePath = $responseData['result']['file_path'];
            $imageUrl = "https://api.telegram.org/file/bot{$botToken}/{$filePath}";

            // Download the thumbnail
            $imageResponse = Http::timeout(60)->get($imageUrl);

            if (!$imageResponse->successful()) {
                Log::error('Failed to download thumbnail', [
                    'messageId' => $messageId,
                    'fileId' => $fileId,
                    'status' => $imageResponse->status()
                ]);
                return null;
            }

            // Store the thumbnail in public folder
            $extension = pathinfo($filePath, PATHINFO_EXTENSION) ?: 'jpg';
            $filename = 'telegram_thumb_' . $messageId . '_' . time() . '.' . $extension;

            $publicDir = public_path('telegram_thumbnails');
            if (!is_dir($publicDir)) {
                mkdir($publicDir, 0755, true);
            }

            $fullPath = $publicDir . '/' . $filename;
            file_put_contents($fullPath, $imageResponse->body());

            $storedPath = 'telegram_thumbnails/' . $filename;

            Log::info('Thumbnail stored successfully', [
                'messageId' => $messageId,
                'path' => $storedPath,
                'fullPath' => $fullPath,
                'size' => strlen($imageResponse->body())
            ]);

            return $storedPath;
        } catch (Exception $e) {
            Log::error('Error in downloadThumbnail: ' . $e->getMessage(), [
                'messageId' => $messageId ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Download file from Telegram
     *
     * @param string $fileId The file ID
     * @param ProductDocSocial $socialConfig The social configuration
     * @param string $directory The directory to store the file
     * @return string|null The path to the stored file or null if failed
     */
    private function downloadFile($fileId, $socialConfig, $directory)
    {
        try {
            // Get file path from Telegram
            $botToken = $socialConfig->social_token;
            $url = "https://api.telegram.org/bot{$botToken}/getFile?file_id={$fileId}";
            
            $response = Http::timeout(30)->get($url);
            $fileInfo = $response->json();
            
            if (!$response->successful() || !isset($fileInfo['ok']) || !$fileInfo['ok']) {
                Log::error('Failed to get file info', [
                    'fileId' => $fileId,
                    'response' => $fileInfo
                ]);
                return null;
            }
            
            $filePath = $fileInfo['result']['file_path'];
            $downloadUrl = "https://api.telegram.org/file/bot{$botToken}/{$filePath}";
            
            // Create directory if it doesn't exist
            $storagePath = "telegram/{$directory}/" . $socialConfig->business_id;
            Storage::makeDirectory($storagePath);
            
            // Download and save the file
            $fileName = basename($filePath);
            $fullPath = "{$storagePath}/{$fileName}";
            
            $fileResponse = Http::timeout(60)->get($downloadUrl);
            
            if (!$fileResponse->successful()) {
                Log::error('Failed to download file', [
                    'fileId' => $fileId,
                    'status' => $fileResponse->status()
                ]);
                return null;
            }
            
            Storage::put($fullPath, $fileResponse->body());
            
            Log::info("File downloaded to: {$fullPath}");
            
            return $fullPath;
        } catch (Exception $e) {
            Log::error('Error in downloadFile: ' . $e->getMessage(), [
                'fileId' => $fileId ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Send a message to a Telegram chat
     *
     * @param int|string $chatId The chat ID
     * @param string $text The message text
     * @param string $botToken The bot token
     * @return bool Whether the message was sent successfully
     */
    protected function sendMessage($chatId, $text, $botToken)
    {
        try {
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
            $response = Http::post($url, [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);
            
            return $response->successful();
        } catch (Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage(), [
                'chatId' => $chatId,
                'text' => $text
            ]);
            return false;
        }
    }
}