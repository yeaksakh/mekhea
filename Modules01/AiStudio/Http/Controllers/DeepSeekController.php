<?php

namespace Modules\AiStudio\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DeepSeekController extends Controller
{
    protected function getJsonBasePath()
    {
        $userId = Auth::id();
        if (!$userId) {
            abort(401, 'User not authenticated.');
        }
        return 'Modules/AiStudio/History/deepseek/' . $userId;
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

            return view('aistudio::deepseek.index', compact('messages', 'conversations', 'currentConversationId'));
        } catch (\Exception $e) {
            Log::error('Deepseek Index Error: ' . $e->getMessage());
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
                if ($msg['role'] === 'user') {
                    return [
                        'role' => 'user',
                        'content' => $msg['text']
                    ];
                }
            }, $messages);
            $conversationHistory = array_values(array_filter($conversationHistory));

            $openRouterKey = env('OPENROUTER_API_KEY', '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');
            $openRouterUrl = 'https://openrouter.ai/api/v1/chat/completions';
            $modelToUse = 'tngtech/deepseek-r1t2-chimera:free';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $openRouterKey,
                'Content-Type' => 'application/json',
            ])
            ->timeout(90)
            ->connectTimeout(15)
            ->post($openRouterUrl, [
                'model' => $modelToUse,
                'messages' => $conversationHistory,
                'temperature' => 0.7,
            ]);

            if ($response->failed()) {
                $errorMessage = 'OpenRouter.ai DeepSeek API call failed: ' . $response->status() . ' - ' . $response->body();
                Log::error($errorMessage, ['api_response' => $response->json()]);
                $reply = $errorMessage;
            } else {
                $responseData = $response->json();
                $reply = $responseData['choices'][0]['message']['content'] ?? 'Sorry, no response.';
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
            Log::error('Deepseek Send Error: ' . $e->getMessage());
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
                return [
                    'role' => $msg['role'],
                    'content' => $text,
                ];
            }, array_slice($messages, 0, $messageIndex + 1), array_keys(array_slice($messages, 0, $messageIndex + 1)));

            $openRouterKey = env('OPENROUTER_API_KEY', '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');
            $openRouterUrl = 'https://openrouter.ai/api/v1/chat/completions';
            $modelToUse = 'deepseek/deepseek-chat';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $openRouterKey,
                'Content-Type' => 'application/json',
            ])
            ->timeout(90)
            ->connectTimeout(15)
            ->post($openRouterUrl, [
                'model' => $modelToUse,
                'messages' => $conversationHistory,
                'temperature' => 0.7,
            ]);

            if ($response->failed()) {
                $errorMessage = 'OpenRouter.ai DeepSeek API call failed: ' . $response->status() . ' - ' . $response->body();
                Log::error($errorMessage, ['api_response' => $response->json()]);
                $reply = $errorMessage;
            } else {
                $responseData = $response->json();
                $reply = $responseData['choices'][0]['message']['content'] ?? 'Sorry, no response.';
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

            $messages = array_slice($messages, 0, $messageIndex + 2);

            $this->saveMessages($conversationId, $messages);

            return response()->json([
                'reply' => $reply,
                'messages' => $messages,
                'conversation_id' => $conversationId,
                'conversations' => array_values($this->sortConversations($conversations)),
            ]);
        } catch (\Exception $e) {
            Log::error('Deepseek Edit Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to edit message: ' . $e->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $conversationId = $request->input('conversation_id');
            $conversations = $this->getConversations();
            if ($conversationId && isset($conversations[$conversationId])) {
                $filename = $conversations[$conversationId]['filename'];
                $path = base_path("{$this->getJsonBasePath()}/{$filename}");
                if (File::exists($path)) {
                    File::delete($path);
                }
                unset($conversations[$conversationId]);
                $this->saveConversations($conversations);
            }
            return response()->json(['conversations' => array_values($this->sortConversations($this->getConversations()))]);
        } catch (\Exception $e) {
            Log::error('Deepseek Delete Error: ' . $e->getMessage());
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
            Log::error('Deepseek New Error: ' . $e->getMessage());
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
            $newTitle = $input['title'];
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
            Log::error('Deepseek Rename Error: ' . $e->getMessage());
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
            return json_decode(File::get($path), true) ?: [];
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
            return json_decode(File::get($path), true) ?: [];
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
        return Str::limit($sanitized, 50, '') ?: 'Untitled';
    }
}
