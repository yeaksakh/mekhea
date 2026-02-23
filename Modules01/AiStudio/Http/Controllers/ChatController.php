<?php

namespace Modules\AiStudio\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    protected function getJsonBasePath()
    {
        $userId = Auth::id();
        if (!$userId) {
            abort(401, 'User not authenticated.');
        }
        return 'Modules/AiStudio/History/aistudio/' . $userId;
    }

    public function index(Request $request)
    {
        try {
            $conversations = $this->getConversations();
            $currentConversationId = $request->query('conversation_id', array_key_first($conversations) ?? null);
            $messages = $currentConversationId ? $this->getMessages($currentConversationId) : [];

            if ($request->expectsJson()) {
                return response()->json([
                    'messages' => $messages,
                    'conversations' => array_values($this->sortConversations($conversations)),
                ]);
            }

            return view('aistudio::chat.index', compact('messages', 'conversations', 'currentConversationId'));
        } catch (\Exception $e) {
            Log::error('Chat Index Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load conversations: ' . $e->getMessage()], 500);
        }
    }

    public function send(Request $request)
    {
        try {
            $input = $request->validate([
                'message' => 'required|string',
                'conversation_id' => 'nullable|string',
            ]);

            $userMessage = $input['message'];
            $conversationId = $input['conversation_id'] ?? Str::uuid()->toString();

            $conversations = $this->getConversations();
            if (!isset($conversations[$conversationId])) {
                $title = Str::limit($userMessage, 50, '...');
                $sanitizedTitle = $this->sanitizeTitle($title);
                $filename = "{$sanitizedTitle}_{$conversationId}.json";
                $conversations[$conversationId] = [
                    'id' => $conversationId,
                    'title' => $title,
                    'filename' => $filename,
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toIso8601String(),
                ];
                $this->saveConversations($conversations);
            } else {
                $conversations[$conversationId]['updated_at'] = now()->toIso8601String();
                $this->saveConversations($conversations);
            }

            $messages = $this->getMessages($conversationId);
            $messages[] = [
                'role' => 'user',
                'text' => $userMessage,
                'timestamp' => now()->format('h:i A'),
                'originalText' => $userMessage,
                'originalResponse' => null,
                'versions' => [],
                'currentVersion' => 0,
            ];

            $conversationHistory = array_map(function ($msg) {
                $role = $msg['role'] === 'ai' ? 'model' : $msg['role'];
                return [
                    'role' => $role,
                    'parts' => [['text' => $msg['text']]],
                ];
            }, $messages);

            $model = 'gemini-2.5-flash-lite-preview-06-17';
            $apiKey = env('GOOGLE_GEMINI_KEY', 'AIzaSyAb7OrASNz7LBV5gEkSagpU6gEjg2ZQxQs');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey;

            $body = [
                'contents' => $conversationHistory,
                'generationConfig' => [
                    'maxOutputTokens' => 65536,
                    'temperature' => 2,
                ],
            ];

            $response = Http::withHeaders(['Content-Type' => 'application/json'])->post($url, $body);

            if (!$response->successful()) {
                Log::error('AI API Error: ' . $response->body());
                $reply = 'AI API returned an error: ' . ($response->json()['error']['message'] ?? 'Unknown error');
            } else {
                $json = $response->json();
                $reply = $json['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, no response.';
            }

            $messages[count($messages) - 1]['originalResponse'] = $reply;
            $messages[] = [
                'role' => 'ai',
                'text' => $reply,
                'timestamp' => now()->format('h:i A'),
            ];

            $this->saveMessages($conversationId, $messages);

            return response()->json([
                'reply' => $reply,
                'messages' => $messages,
                'conversation_id' => $conversationId,
                'conversations' => array_values($this->sortConversations($conversations)),
            ]);
        } catch (\Exception $e) {
            Log::error('Chat Send Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send message: ' . $e->getMessage()], 500);
        }
    }

    public function edit(Request $request)
    {
        try {
            $input = $request->validate([
                'conversation_id' => 'required|string',
                'message_index' => 'required|integer|min:0',
                'message' => 'required|string',
            ]);

            $conversationId = $input['conversation_id'];
            $messageIndex = $input['message_index'];
            $newMessage = $input['message'];

            $conversations = $this->getConversations();
            if (!isset($conversations[$conversationId])) {
                return response()->json(['error' => 'Conversation not found'], 404);
            }

            $messages = $this->getMessages($conversationId);
            if (!isset($messages[$messageIndex]) || $messages[$messageIndex]['role'] !== 'user') {
                return response()->json(['error' => 'Invalid message index or message is not editable'], 400);
            }

            if (!isset($messages[$messageIndex]['versions'])) {
                $messages[$messageIndex]['versions'] = [];
                $messages[$messageIndex]['originalText'] = $messages[$messageIndex]['text'];
                $messages[$messageIndex]['originalResponse'] = isset($messages[$messageIndex + 1]) && $messages[$messageIndex + 1]['role'] === 'ai' ? $messages[$messageIndex + 1]['text'] : null;
                $messages[$messageIndex]['currentVersion'] = 0;
            }

            $conversationHistory = array_map(function ($msg, $index) use ($messageIndex, $newMessage) {
                $text = $index === $messageIndex ? $newMessage : $msg['text'];
                $role = $msg['role'] === 'ai' ? 'model' : $msg['role'];
                return [
                    'role' => $role,
                    'parts' => [['text' => $text]],
                ];
            }, array_slice($messages, 0, $messageIndex + 1), array_keys(array_slice($messages, 0, $messageIndex + 1)));

            $model = 'gemini-1.5-flash-latest';
            $apiKey = env('GOOGLE_API_KEY', 'AIzaSyDjkRrReRaUV888Aj9LPEktT1c-vMrTJK0');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey;

            $body = [
                'contents' => $conversationHistory,
                'generationConfig' => [
                    'maxOutputTokens' => 65536,
                    'temperature' => 2,
                ],
            ];

            $response = Http::withHeaders(['Content-Type' => 'application/json'])->post($url, $body);

            if (!$response->successful()) {
                Log::error('AI API Error on Edit: ' . $response->body());
                $reply = 'AI API returned an error: ' . ($response->json()['error']['message'] ?? 'Unknown error');
            } else {
                $json = $response->json();
                $reply = $json['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, no response.';
            }

            $messages[$messageIndex]['versions'][] = [
                'text' => $newMessage,
                'response' => $reply,
                'timestamp' => now()->format('h:i A'),
            ];
            $messages[$messageIndex]['text'] = $newMessage;
            $messages[$messageIndex]['timestamp'] = now()->format('h:i A');
            $messages[$messageIndex]['currentVersion'] = count($messages[$messageIndex]['versions']);

            if ($messageIndex === 0) {
                $title = Str::limit($newMessage, 50, '...');
                $sanitizedTitle = $this->sanitizeTitle($title);
                $oldFilename = $conversations[$conversationId]['filename'];
                $newFilename = "{$sanitizedTitle}_{$conversationId}.json";
                $oldPath = base_path("{$this->getJsonBasePath()}/{$oldFilename}");
                $newPath = base_path("{$this->getJsonBasePath()}/{$newFilename}");

                if (File::exists($oldPath) && $oldFilename !== $newFilename) {
                    File::move($oldPath, $newPath);
                }

                $conversations[$conversationId]['title'] = $title;
                $conversations[$conversationId]['filename'] = $newFilename;
                $conversations[$conversationId]['updated_at'] = now()->toIso8601String();
                $this->saveConversations($conversations);
            }

            // Replace the existing AI response if it exists, otherwise append
            if (isset($messages[$messageIndex + 1]) && $messages[$messageIndex + 1]['role'] === 'ai') {
                $messages[$messageIndex + 1] = [
                    'role' => 'ai',
                    'text' => $reply,
                    'timestamp' => now()->format('h:i A'),
                ];
            } else {
                $messages[] = [
                    'role' => 'ai',
                    'text' => $reply,
                    'timestamp' => now()->format('h:i A'),
                ];
            }

            // Truncate any messages after the AI response
            $messages = array_slice($messages, 0, $messageIndex + 2);

            $this->saveMessages($conversationId, $messages);

            return response()->json([
                'reply' => $reply,
                'messages' => $messages,
                'conversation_id' => $conversationId,
                'conversations' => array_values($this->sortConversations($conversations)),
            ]);
        } catch (\Exception $e) {
            Log::error('Chat Edit Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to edit message: ' . $e->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $conversationId = $request->input('conversation_id');
            if ($conversationId) {
                $conversations = $this->getConversations();
                if (isset($conversations[$conversationId])) {
                    $filename = $conversations[$conversationId]['filename'];
                    $path = base_path("{$this->getJsonBasePath()}/{$filename}");
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                    unset($conversations[$conversationId]);
                    $this->saveConversations($conversations);
                }
            }
            return response()->json(['conversations' => array_values($this->sortConversations($conversations))]);
        } catch (\Exception $e) {
            Log::error('Chat Delete Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete conversation: ' . $e->getMessage()], 500);
        }
    }

    public function new(Request $request)
    {
        try {
            $conversationId = Str::uuid()->toString();
            $title = 'New Chat';
            $sanitizedTitle = $this->sanitizeTitle($title);
            $filename = "{$sanitizedTitle}_{$conversationId}.json";
            $conversations = $this->getConversations();
            $conversations[$conversationId] = [
                'id' => $conversationId,
                'title' => $title,
                'filename' => $filename,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toIso8601String(),
            ];
            $this->saveConversations($conversations);
            return response()->json([
                'conversation_id' => $conversationId,
                'conversations' => array_values($this->sortConversations($conversations)),
            ]);
        } catch (\Exception $e) {
            Log::error('Chat New Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create new conversation: ' . $e->getMessage()], 500);
        }
    }

    public function rename(Request $request)
    {
        try {
            $input = $request->validate([
                'conversation_id' => 'required|string',
                'title' => 'required|string|max:100',
            ]);

            $conversationId = $input['conversation_id'];
            $newTitle = str_replace(['/', '\\', ':'], '_', $input['title']);
            $conversations = $this->getConversations();
            if (isset($conversations[$conversationId])) {
                $oldFilename = $conversations[$conversationId]['filename'];
                $sanitizedTitle = $this->sanitizeTitle($newTitle);
                $newFilename = "{$sanitizedTitle}_{$conversationId}.json";
                $oldPath = base_path("{$this->getJsonBasePath()}/{$oldFilename}");
                $newPath = base_path("{$this->getJsonBasePath()}/{$newFilename}");

                if (File::exists($oldPath) && $oldFilename !== $newFilename) {
                    File::move($oldPath, $newPath);
                }

                $conversations[$conversationId]['title'] = $newTitle;
                $conversations[$conversationId]['filename'] = $newFilename;
                $conversations[$conversationId]['updated_at'] = now()->toIso8601String();
                $this->saveConversations($conversations);
            }
            return response()->json(['conversations' => array_values($this->sortConversations($conversations))]);
        } catch (\Exception $e) {
            Log::error('Chat Rename Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to rename conversation: ' . $e->getMessage()], 500);
        }
    }

    protected function getMessages($conversationId)
    {
        $conversations = $this->getConversations();
        if (!isset($conversations[$conversationId])) {
            return [];
        }
        $filename = $conversations[$conversationId]['filename'];
        $path = base_path("{$this->getJsonBasePath()}/{$filename}");
        if (File::exists($path)) {
            $contents = File::get($path);
            return json_decode($contents, true) ?: [];
        }
        return [];
    }

    protected function saveMessages($conversationId, array $messages)
    {
        $conversations = $this->getConversations();
        if (!isset($conversations[$conversationId])) {
            return;
        }
        $filename = $conversations[$conversationId]['filename'];
        $path = base_path("{$this->getJsonBasePath()}/{$filename}");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode($messages, JSON_PRETTY_PRINT));
    }

    protected function getConversations()
    {
        $path = base_path("{$this->getJsonBasePath()}/conversations.json");
        if (File::exists($path)) {
            $contents = File::get($path);
            return json_decode($contents, true) ?: [];
        }
        return [];
    }

    protected function saveConversations(array $conversations)
    {
        $path = base_path("{$this->getJsonBasePath()}/conversations.json");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode($conversations, JSON_PRETTY_PRINT));
    }

    protected function sortConversations(array $conversations)
    {
        usort($conversations, function ($a, $b) {
            $aTime = isset($a['updated_at']) ? strtotime($a['updated_at']) : strtotime($a['created_at']);
            $bTime = isset($b['updated_at']) ? strtotime($b['updated_at']) : strtotime($b['created_at']);
            return $bTime <=> $aTime;
        });
        return $conversations;
    }

    protected function sanitizeTitle($title)
    {
        $sanitized = preg_replace('/[^a-zA-Z0-9\s_-]/', '', $title);
        $sanitized = str_replace(' ', '_', trim($sanitized));
        $sanitized = Str::limit($sanitized, 50, '');
        return $sanitized ?: 'Untitled';
    }
}
