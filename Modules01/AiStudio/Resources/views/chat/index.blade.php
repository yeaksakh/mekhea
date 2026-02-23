<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/marked@4.0.0/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/highlight.js@11.7.0/lib/core.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/highlight.js@11.7.0/lib/languages/cpp.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/highlight.js@11.7.0/styles/atom-one-light.min.css" id="highlight-style">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

<style>
    .code-block {
        width: 100%;
        overflow-x: auto;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 0.5rem;
        background-color: #f5f5f5;
        color: #1f2a44;
    }
    .dark .code-block {
        background-color: #2d2d2d;
        color: #e5e7eb;
    }
    pre, code {
        font-family: 'Fira Code', monospace;
        font-size: 0.875rem;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    .chat-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 1000;
        display: flex;
        background-color: #ffffff;
    }
    .dark .chat-container {
        background-color: #0f172a;
    }
    textarea {
        resize: none;
        overflow-y: auto; /* Allow scrolling for long input */
    }
    .fade-in-out {
        transition: opacity 0.5s, transform 0.5s;
    }
    .dark * {
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }
    .sidebar {
        width: 250px;
        flex-shrink: 0;
        overflow-y: auto;
        background-color: #f9fafb;
        border-right: 1px solid #e5e7eb;
    }
    .dark .sidebar {
        background-color: #1e293b;
        border-right-color: #334155;
    }
    .chat-content {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        min-width: 0; /* Prevents content from overflowing flex container */
    }
    .dropdown-menu {
        min-width: 100px;
    }
    .chat-box {
        flex-grow: 1;
        overflow-y: auto;
        padding: 1rem;
        background-color: #f9fafb;
    }
    .dark .chat-box {
        background-color: #1e293b;
    }
    .message-bubble {
        max-width: 75%;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
        word-wrap: break-word;
        overflow-wrap: break-word;
        position: relative;
        overflow: hidden; /* Prevents child elements (like code blocks) from overflowing the bubble */
    }
    .user-message {
        background-color: #2563eb;
        color: #ffffff;
    }
    .dark .user-message {
        background-color: #3b82f6;
        color: #ffffff;
    }
    .ai-message {
        background-color: #ffffff;
        color: #000000;
    }
    .dark .ai-message {
        background-color: #374151;
        color: #ffffff;
    }
    .header {
        background-color: #ffffff;
        border-bottom: 1px solid #e5e7eb;
    }
    .dark .header {
        background-color: #1e293b;
        border-bottom-color: #374151;
    }
    .input-area {
        margin: 0;
        background-color: #ffffff;
        border-top: 1px solid #e5e7eb;
    }
    .dark .input-area {
        background-color: #1e293b;
        border-top-color: #374151;
    }
    .textarea {
        background-color: #ffffff;
        border: 1px solid #d1d5db;
        color: #1f2a44;
        resize: none;
    }
    .dark .textarea {
        background-color: #1f2937;
        border-color: #4b5563;
        color: #e5e7eb;
    }
    .textarea::placeholder {
        color: #6b7280;
    }
    .dark .textarea::placeholder {
        color: #9ca3af;
    }
    .button {
        background-color: #2563eb;
        color: #ffffff;
    }
    .dark .button {
        background-color: #3b82f6;
        color: #ffffff;
    }
    .button:hover {
        background-color: #1d4ed8;
    }
    .dark .button:hover {
        background-color: #2563eb;
    }
    .theme-button {
        background-color: #e5e7eb;
        color: #1f2a44;
    }
    .dark .theme-button {
        background-color: #4b5563;
        color: #e5e7eb;
    }
    .theme-button:hover {
        background-color: #d1d5db;
    }
    .dark .theme-button:hover {
        background-color: #6b7280;
    }
    .conversation-item {
        background-color: transparent;
        color: #1f2a44;
    }
    .conversation-item:hover {
        background-color: #f3f4f6;
    }
    .dark .conversation-item {
        color: #e5e7eb;
    }
    .dark .conversation-item:hover {
        background-color: #374151;
    }
    .conversation-item.active {
        background-color: #e5e7eb;
        color: #1f2a44;
    }
    .dark .conversation-item.active {
        background-color: #4b5563;
        color: #e5e7eb;
    }
    .header-title {
        color: #1f2a44;
    }
    .dark .header-title {
        color: #e5e7eb;
    }
    .modal {
        background-color: rgba(0, 0, 0, 0.5);
    }
    .modal-content {
        background-color: #ffffff;
        color: #1f2a44;
    }
    .dark .modal-content {
        background-color: #1e293b;
        color: #e5e7eb;
    }
    .modal-input {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        color: #1f2a44;
    }
    .dark .modal-input {
        background-color: #1f2937;
        border-color: #4b5563;
        color: #e5e7eb;
    }
    .welcome-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        text-align: center;
        color: #1f2a44;
    }
    .dark .welcome-container {
        color: #e5e7eb;
    }
    .welcome-logo {
        width: 100px;
        height: 100px;
        margin-bottom: 1rem;
    }
    .edit-textarea {
        width: 100%;
        min-height: 100px;
        padding: 0.5rem;
        border-radius: 0.375rem;
    }
    .message-actions {
        display: none;
        gap: 0.5rem;
    }
    .message-bubble:hover .message-actions {
        display: flex;
    }
    .message-footer {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.25rem;
    }
    .nav-buttons {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    .edited-label {
        font-size: 0.75rem;
        color: #ffffff;
        margin-top: 0.25rem;
    }
    .dark .edited-label {
        color: #ffffff;
    }
</style>

<div
    x-data="chatApp()"
    x-init="initTheme(); initializeMessages()"
    class="chat-container transition-colors duration-300"
    :class="{ 'dark': isDark }"
>
    <div class="sidebar flex flex-col">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 header">
            <h2 class="text-lg font-bold header-title">Conversations</h2>
            <button
                @click="newConversation()"
                class="mt-2 w-full button px-4 py-2 rounded-lg transition"
            >
                New Chat
            </button>
        </div>
        <div class="flex-grow overflow-y-auto">
            <template x-for="conv in conversations" :key="conv.id">
                <div
                    class="p-3 flex justify-between items-center cursor-pointer relative conversation-item"
                    :class="{ 'active': currentConversationId === conv.id }"
                >
                    <span
                        class="truncate flex-grow pr-6"
                        @click="loadConversation(conv.id)"
                        x-text="conv.title"
                    ></span>
                    <div class="relative">
                        <button
                            @click="toggleDropdown(conv.id)"
                            class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                            </svg>
                        </button>
                        <div
                            x-show="openDropdown === conv.id"
                            x-transition
                            class="absolute right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg dropdown-menu z-10"
                            @click.away="openDropdown = null"
                        >
                            <button
                                @click="openRenameModal(conv.id, conv.title)"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                Rename
                            </button>
                            <button
                                @click="deleteConversation(conv.id)"
                                class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div class="chat-content flex flex-col">
        <div class="flex justify-between items-center p-4 header flex-shrink-0">
            <h1 class="text-xl font-bold header-title">Jester AI</h1>
            <div class="flex items-center gap-2">
                <a href="/home"
                    class="button px-4 py-2 rounded-lg transition no-underline"
                    title="Close Chat"
                >
                    Close
                </a>
                <button
                    @click="toggleTheme()"
                    class="p-2 rounded-full theme-button transition"
                    title="Toggle Theme"
                >
                    <svg x-show="isDark" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg x-show="!isDark" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </div>
        </div>

        <div
            id="chat-box"
            class="chat-box"
            style="scroll-behavior: smooth;"
        >
            <template x-if="messages.length === 0 && !typingMessage">
                <div class="welcome-container">
                    @php $user = Auth::user(); @endphp
                    @if ($user->image)
                        <a href="{{ $user->image }}" target="_blank">
                            <img src="{{ $user->image }}" alt="Profile Image"
                                class="w-16 h-16 rounded-full object-cover border border-white welcome-logo">
                        </a>
                    @else
                        <i class="fas fa-user-circle text-black text-6xl"></i>
                    @endif
                    <h2 class="text-2xl font-bold header-title">Welcome, {{ auth()->user()->username ?? 'User' }}!</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Start a conversation with Jester AI.</p>
                </div>
            </template>
            <template x-for="(msg, index) in messages" :key="index + '-' + (msg.currentVersion || 0)">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div class="flex flex-col message-bubble" :class="msg.role === 'user' ? 'user-message' : 'ai-message'">
                        <template x-if="editingMessageIndex === index">
                            <div>
                                <textarea
                                    x-model="editingMessageText"
                                    class="edit-textarea textarea rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition"
                                    @keydown.enter.prevent="saveEditedMessage(index)"
                                    @keydown.esc="cancelEdit()"
                                ></textarea>
                                <div class="mt-2 flex justify-end gap-2">
                                    <button
                                        @click="cancelEdit()"
                                        class="text-xs text-gray-500 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        @click="saveEditedMessage(index)"
                                        class="text-xs text-blue-500 dark:text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 transition"
                                        :disabled="!editingMessageText.trim()"
                                    >
                                        Save
                                    </button>
                                </div>
                            </div>
                        </template>
                        <template x-if="editingMessageIndex !== index">
                            <div>
                                <div x-html="renderMessage(msg.text)"></div>
                                <div x-show="msg.versions && msg.versions.length > 0" class="nav-buttons">
                                    <button
                                        @click="prevVersion(index)"
                                        :disabled="msg.currentVersion === 0"
                                        class="text-xs text-white dark:text-white hover:text-blue-500 dark:hover:text-blue-400 transition"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button
                                        @click="nextVersion(index)"
                                        :disabled="msg.currentVersion === (msg.versions ? msg.versions.length : 0)"
                                        class="text-xs text-white dark:text-white hover:text-blue-500 dark:hover:text-blue-400 transition"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                    <div class="edited-label">Edited</div>
                                </div>
                                <div class="message-footer" :class="msg.role === 'user' ? 'justify-start' : 'justify-start'">
                                    <div class="text-xs" x-text="msg.timestamp"></div>
                                    <div class="message-actions">
                                        <button
                                            @click="copyToClipboard(msg.text)"
                                            class="text-xs text-gray-500 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition"
                                            :title="msg.role === 'user' ? 'Copy Message' : 'Copy Response'"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                        <template x-if="msg.role === 'user'">
                                            <button
                                                @click="editMessage(index, msg.text)"
                                                class="text-xs text-gray-500 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition"
                                                title="Edit Message"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <div x-show="isLoading && !typingMessage" class="flex justify-start">
                <div class="message-bubble ai-message">
                    <span class="animate-pulse">Thinking...</span>
                </div>
            </div>
            <div x-show="typingMessage" class="flex justify-start">
                <div class="flex flex-col message-bubble ai-message">
                    <div x-html="renderMessage(typingMessage.text)"></div>
                    <div class="message-footer justify-start">
                        <div class="text-xs text-black dark:text-white" x-text="typingMessage.timestamp"></div>
                        <div class="message-actions">
                            <button
                                @click="copyToClipboard(typingMessage.text)"
                                class="text-xs text-gray-500 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition"
                                title="Copy Response"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form @submit.prevent="sendMessage()" class="flex gap-3 items-start p-4 input-area flex-shrink-0">
            <textarea
                x-ref="chatInput"
                x-model="input"
                @keydown.enter="handleKeydown($event)"
                @input="adjustTextareaHeight($event)"
                class="flex-grow textarea rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition disabled:opacity-50"
                placeholder="I'm here to assist you."
                required
                rows="1"
                style="white-space: pre-wrap;"
                :disabled="isLoading || typingMessage"
            ></textarea>
            <button
                type="submit"
                class="button px-6 py-2 rounded-lg transition disabled:opacity-50 self-end"
                :disabled="isLoading || typingMessage"
            >
                Send
            </button>
        </form>
    </div>

    <div
        x-show="showCopyMessage"
        x-transition:enter="fade-in-out"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="fade-in-out"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="absolute bottom-20 left-1/2 -translate-x-1/2 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 text-sm px-4 py-2 rounded-md shadow-lg"
        style="display: none;"
    >
        <span x-text="copySuccessMessage"></span>
    </div>

    <div
        x-show="showRenameModal"
        x-transition:enter="fade-in-out"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="fade-in-out"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="modal fixed inset-0 flex items-center justify-center z-50"
        @click.away="closeRenameModal()"
    >
        <div class="modal-content rounded-lg shadow-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold header-title mb-4">Rename Conversation</h3>
            <input
                x-model="renameInput"
                type="text"
                class="modal-input w-full px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition mb-4"
                placeholder="Enter new title"
            >
            <div class="flex justify-end gap-2">
                <button
                    @click="closeRenameModal()"
                    class="button px-4 py-2 rounded-lg transition bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-500"
                >
                    Cancel
                </button>
                <button
                    @click="renameConversation()"
                    class="button px-4 py-2 rounded-lg transition"
                    :disabled="!renameInput.trim()"
                >
                    Save
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chatApp', () => ({
            input: '',
            messages: @json($messages ?? []),
            conversations: @json(array_values($conversations ?? [])),
            currentConversationId: @json($currentConversationId ?? null),
            isLoading: false,
            isDark: false,
            showCopyMessage: false,
            copySuccessMessage: '',
            openDropdown: null,
            showRenameModal: false,
            renameInput: '',
            renameConversationId: null,
            newestConversationId: localStorage.getItem('newestConversationId') || null,
            messageCache: {},
            typingMessage: null,
            editingMessageIndex: null,
            editingMessageText: '',

            initTheme() {
                const savedTheme = localStorage.getItem('theme');
                if (savedTheme === 'dark' || savedTheme === 'light') {
                    this.isDark = savedTheme === 'dark';
                } else {
                    this.isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                }
                this.applyTheme();
                this.$nextTick(() => this.applyTheme());
                this.sortConversations();
                if (this.currentConversationId && this.messages.length) {
                    this.messageCache[this.currentConversationId] = this.messages;
                }
            },

            initializeMessages() {
                this.scrollToBottom();
            },

            sortConversations() {
                if (this.newestConversationId) {
                    const newestConv = this.conversations.find(c => c.id === this.newestConversationId);
                    if (newestConv) {
                        this.conversations = [
                            newestConv,
                            ...this.conversations.filter(c => c.id !== this.newestConversationId)
                        ];
                    }
                }
            },

            toggleTheme() {
                this.isDark = !this.isDark;
                localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                this.applyTheme();
            },

            applyTheme() {
                const html = document.documentElement;
                if (this.isDark) {
                    html.classList.add('dark');
                    document.getElementById('highlight-style').href = 'https://cdn.jsdelivr.net/npm/highlight.js@11.7.0/styles/atom-one-dark.min.css';
                } else {
                    html.classList.remove('dark');
                    document.getElementById('highlight-style').href = 'https://cdn.jsdelivr.net/npm/highlight.js@11.7.0/styles/atom-one-light.min.css';
                }
            },

            copyToClipboard(text) {
                const cleanText = text.replace(/```[\w]*\n([\s\S]*?)\n```/g, '$1').trim();
                navigator.clipboard.writeText(cleanText).then(() => {
                    this.copySuccessMessage = 'Message copied to clipboard!';
                    this.showCopyMessage = true;
                    setTimeout(() => this.showCopyMessage = false, 2000);
                }).catch((err) => {
                    console.error('Copy failed:', err);
                    this.copySuccessMessage = 'Failed to copy message.';
                    this.showCopyMessage = true;
                    setTimeout(() => this.showCopyMessage = false, 2000);
                });
            },

            toggleDropdown(conversationId) {
                this.openDropdown = this.openDropdown === conversationId ? null : conversationId;
            },

            openRenameModal(conversationId, title) {
                this.renameConversationId = conversationId;
                this.renameInput = title;
                this.showRenameModal = true;
                this.openDropdown = null;
            },

            closeRenameModal() {
                this.showRenameModal = false;
                this.renameInput = '';
                this.renameConversationId = null;
            },

            renameConversation() {
                if (!this.renameInput.trim()) return;

                fetch('{{ route("jester.chat.rename") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        conversation_id: this.renameConversationId,
                        title: this.renameInput.trim(),
                    }),
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    this.conversations = data.conversations;
                    this.sortConversations();
                    this.closeRenameModal();
                })
                .catch(error => {
                    console.error('Rename Conversation Error:', error);
                    this.messages.push({
                        role: 'ai',
                        text: `Failed to rename conversation: ${error.message}`,
                        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                    });
                    this.scrollToBottom();
                });
            },

            newConversation() {
                fetch('{{ route("jester.chat.new") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    this.newestConversationId = data.conversation_id;
                    localStorage.setItem('newestConversationId', this.newestConversationId);
                    this.conversations = [data.conversations.find(c => c.id === data.conversation_id), ...this.conversations.filter(c => c.id !== data.conversation_id)];
                    this.currentConversationId = data.conversation_id;
                    this.messages = [];
                    this.messageCache[data.conversation_id] = [];
                    this.scrollToBottom();
                })
                .catch(error => {
                    console.error('New Conversation Error:', error);
                    this.messages.push({
                        role: 'ai',
                        text: `Failed to create new conversation: ${error.message}`,
                        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                    });
                    this.scrollToBottom();
                });
            },

            loadConversation(conversationId) {
                window.history.pushState({}, '', `?conversation_id=${conversationId}`);
                this.currentConversationId = conversationId;
                if (this.typingMessage) {
                    this.messages.push(this.typingMessage);
                    this.messageCache[this.currentConversationId] = this.messages;
                    this.typingMessage = null;
                }
                if (this.editingMessageIndex !== null) {
                    this.cancelEdit();
                }
                if (conversationId !== this.newestConversationId) {
                    this.newestConversationId = null;
                    localStorage.removeItem('newestConversationId');
                }

                if (this.messageCache[conversationId]) {
                    this.messages = this.messageCache[conversationId];
                    const selectedConv = this.conversations.find(c => c.id === conversationId);
                    if (selectedConv && conversationId !== this.newestConversationId) {
                        this.conversations = [
                            selectedConv,
                            ...this.conversations.filter(c => c.id !== conversationId)
                        ];
                    }
                    this.sortConversations();
                    this.scrollToBottom();
                    return;
                }

                fetch(`{{ route('jester.chat.index') }}?conversation_id=${conversationId}`, {
                    headers: {
                        'Accept': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        return response.text().then(text => {
                            throw new Error(`Invalid response: Expected JSON, got ${text.substring(0, 50)}...`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    this.messages = data.messages || [];
                    this.messageCache[conversationId] = this.messages;
                    const selectedConv = this.conversations.find(c => c.id === conversationId);
                    if (selectedConv && conversationId !== this.newestConversationId) {
                        this.conversations = [
                            selectedConv,
                            ...this.conversations.filter(c => c.id !== conversationId)
                        ];
                    }
                    this.conversations = data.conversations || this.conversations;
                    this.sortConversations();
                    this.scrollToBottom();
                })
                .catch(error => {
                    console.error('Load Conversation Error:', error);
                    this.messages.push({
                        role: 'ai',
                        text: `Failed to load conversation: ${error.message}`,
                        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                    });
                    this.scrollToBottom();
                });
            },

            deleteConversation(conversationId) {
                if (!confirm('Are you sure you want to delete this conversation?')) return;

                fetch('{{ route("jester.chat.delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ conversation_id: conversationId }),
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    this.conversations = data.conversations;
                    delete this.messageCache[conversationId];
                    if (this.newestConversationId === conversationId) {
                        this.newestConversationId = null;
                        localStorage.removeItem('newestConversationId');
                    }
                    if (this.currentConversationId === conversationId) {
                        this.currentConversationId = this.conversations.length > 0 ? this.conversations[0].id : null;
                        this.messages = this.currentConversationId ? (this.messageCache[this.currentConversationId] || []) : [];
                        window.history.pushState({}, '', this.currentConversationId ? `?conversation_id=${this.currentConversationId}` : '');
                    }
                    this.sortConversations();
                    this.scrollToBottom();
                    this.openDropdown = null;
                })
                .catch(error => {
                    console.error('Delete Conversation Error:', error);
                    this.messages.push({
                        role: 'ai',
                        text: `Failed to delete conversation: ${error.message}`,
                        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                    });
                    this.scrollToBottom();
                });
            },

            renderMessage(text) {
                if (typeof text !== 'string') {
                    console.error('Invalid message text:', text);
                    return '<span>Error: Invalid message content</span>';
                }
                if (text.includes('```')) {
                    const codeMatch = text.match(/```(\w+)?\n([\s\S]*?)\n```/);
                    if (codeMatch) {
                        const language = codeMatch[1] || 'plaintext';
                        const code = codeMatch[2];
                        if (typeof hljs !== 'undefined') {
                            const highlighted = hljs.highlight(code, { language, ignoreIllegals: true }).value;
                            return `<pre class="code-block"><code class="hljs ${language}">${highlighted}</code></pre>`;
                        }
                    }
                }
                if (typeof marked !== 'undefined') {
                    return marked.parse(text, { breaks: true });
                }
                return text.replace(/\n/g, '<br>');
            },

            typeMessage(text, callback) {
                if (typeof text !== 'string') {
                    console.error('Invalid text for typing:', text);
                    this.typingMessage = {
                        role: 'ai',
                        text: 'Error: Invalid response content',
                        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                    };
                    this.messages.push(this.typingMessage);
                    this.messageCache[this.currentConversationId] = this.messages;
                    this.typingMessage = null;
                    this.scrollToBottom();
                    if (callback) callback();
                    return;
                }
                let index = 0;
                const speed = 1;
                this.typingMessage = {
                    role: 'ai',
                    text: '',
                    timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                };

                const typeNextChar = () => {
                    if (index < text.length) {
                        this.typingMessage.text += text.charAt(index);
                        index++;
                        this.scrollToBottom();
                        setTimeout(typeNextChar, speed);
                    } else {
                        this.messages.push(this.typingMessage);
                        this.messageCache[this.currentConversationId] = this.messages;
                        this.typingMessage = null;
                        this.scrollToBottom();
                        if (callback) callback();
                    }
                };

                typeNextChar();
            },

            editMessage(index, text) {
                this.editingMessageIndex = index;
                this.editingMessageText = text;
                this.$nextTick(() => {
                    const textarea = this.$el.querySelector(`textarea[x-model="editingMessageText"]`);
                    if (textarea) {
                        textarea.style.height = 'auto';
                        textarea.style.height = (textarea.scrollHeight) + 'px';
                        textarea.focus();
                    }
                });
            },

            saveEditedMessage(index) {
                if (!this.editingMessageText.trim()) return;

                this.isLoading = true;
                fetch('{{ route("jester.chat.edit") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        conversation_id: this.currentConversationId,
                        message_index: index,
                        message: this.editingMessageText.trim(),
                    }),
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    this.isLoading = false;
                    this.messages = data.messages;
                    this.messageCache[this.currentConversationId] = this.messages;
                    this.conversations = data.conversations;
                    this.sortConversations();
                    this.editingMessageIndex = null;
                    this.editingMessageText = '';
                    this.copySuccessMessage = 'Message updated!';
                    this.showCopyMessage = true;
                    setTimeout(() => this.showCopyMessage = false, 2000);
                    this.typeMessage(data.reply, () => {
                        this.messageCache[this.currentConversationId] = this.messages;
                    });
                })
                .catch(error => {
                    this.isLoading = false;
                    console.error('Edit Message Error:', error);
                    this.messages.push({
                        role: 'ai',
                        text: `Failed to edit message: ${error.message}`,
                        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                    });
                    this.scrollToBottom();
                });
            },

            cancelEdit() {
                this.editingMessageIndex = null;
                this.editingMessageText = '';
            },

            switchVersion(index, version) {
                const msg = this.messages[index];
                if (version === 0) {
                    msg.text = msg.originalText;
                    if (msg.originalResponse && this.messages[index + 1]) {
                        this.messages[index + 1].text = msg.originalResponse;
                    }
                } else {
                    msg.text = msg.versions[version - 1].text;
                    if (msg.versions[version - 1].response && this.messages[index + 1]) {
                        this.messages[index + 1].text = msg.versions[version - 1].response;
                    }
                }
                msg.currentVersion = version;
                this.messages = [...this.messages];
                this.scrollToBottom();
            },

            prevVersion(index) {
                const msg = this.messages[index];
                if (msg.currentVersion > 0) {
                    this.switchVersion(index, msg.currentVersion - 1);
                }
            },

            nextVersion(index) {
                const msg = this.messages[index];
                if (msg.currentVersion < (msg.versions ? msg.versions.length : 0)) {
                    this.switchVersion(index, msg.currentVersion + 1);
                }
            },

            handleKeydown(event) {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    this.sendMessage();
                }
            },

            adjustTextareaHeight(event) {
                const textarea = event.target;
                textarea.style.height = 'auto';
                textarea.style.height = (textarea.scrollHeight) + 'px';
            },

            sendMessage() {
                if (!this.input.trim() || this.isLoading || this.typingMessage) return;

                const timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                this.messages.push({ 
                    role: 'user', 
                    text: this.input, 
                    timestamp, 
                    originalText: this.input, 
                    originalResponse: null,
                    versions: [], 
                    currentVersion: 0 
                });
                const userText = this.input.trim();
                this.isLoading = true;

                this.input = '';
                this.$nextTick(() => {
                    this.$refs.chatInput.style.height = 'auto';
                });

                this.scrollToBottom();

                fetch('{{ route("jester.chat.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        message: userText,
                        conversation_id: this.currentConversationId,
                    }),
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    this.isLoading = false;
                    this.conversations = data.conversations || [];
                    this.currentConversationId = data.conversation_id;
                    if (!this.newestConversationId) {
                        this.newestConversationId = data.conversation_id;
                        localStorage.setItem('newestConversationId', this.newestConversationId);
                    }
                    this.sortConversations();
                    window.history.pushState({}, '', `?conversation_id=${data.conversation_id}`);
                    
                    this.messages[this.messages.length - 1].originalResponse = data.reply;
                    this.typeMessage(data.reply, () => {
                        this.messageCache[this.currentConversationId] = this.messages;
                    });
                })
                .catch(error => {
                    this.isLoading = false;
                    console.error('Send Message Error:', error);
                    this.typeMessage(`Failed to send message: ${error.message}`, () => {
                        this.messageCache[this.currentConversationId] = this.messages;
                    });
                });
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const box = document.getElementById('chat-box');
                    if (box) {
                        box.scrollTop = box.scrollHeight;
                    }
                });
            },
        }));
    });
</script>