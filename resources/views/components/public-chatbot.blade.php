<!-- Public Chatbot Component -->
<div id="public-chatbot-container"
     class="fixed bottom-4 right-4 md:bottom-6 md:right-6 z-50"
     x-data="{
        open: false,
        messages: [],
        loading: false,
        messageInput: '',
        sessionId: 'session_' + Date.now(),
        chatbotUrl: '{{ route('public.chatbot.send') }}',
        csrfToken: '{{ csrf_token() }}',

        init() {
            this.$watch('messages', () => {
                this.scrollToBottom();
            });
        },

        scrollToBottom() {
            setTimeout(() => {
                const container = document.getElementById('chatbot-messages');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }, 50);
        },

        async sendMessage() {
            if (!this.messageInput.trim() || this.loading) return;

            const userMessage = this.messageInput.trim();
            this.messageInput = '';
            this.addMessage(userMessage, 'user');

            this.loading = true;
            try {
                const response = await fetch(this.chatbotUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify({
                        message: userMessage,
                        session_id: this.sessionId
                    })
                });

                const data = await response.json();
                this.loading = false;

                if (data.success) {
                    this.addMessage(data.response, 'bot');
                } else {
                    this.addMessage('Sorry, I encountered an error. Please try again.', 'bot');
                }
            } catch (error) {
                this.loading = false;
                console.error('Chatbot Error:', error);
                this.addMessage('Sorry, I encountered an error. Please try again or contact our support team at +92 334 2372772.', 'bot');
            }
        },

        sendQuickMessage(message) {
            this.messageInput = message;
            this.sendMessage();
        },

        addMessage(text, type) {
            const now = new Date();
            const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            this.messages.push({
                text: text,
                type: type,
                time: time
            });
        }
     }">
    <!-- Chat Button -->
    <button
        @click="open = !open"
        class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-3 md:p-4 shadow-lg transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 fixed bottom-4 right-4 md:bottom-6 md:right-6 z-50"
        :class="{ 'scale-95': open }"
        aria-label="Open Chatbot">
        <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center" x-show="messages.length > 0 && !open" style="display: none;">1</span>
    </button>

    <!-- Chat Window -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 transform scale-95 translate-y-4"
        class="fixed md:absolute bottom-16 md:bottom-20 right-4 md:right-0 left-4 md:left-auto w-[calc(100%-2rem)] md:w-96 h-[calc(100vh-8rem)] md:h-[500px] max-h-[600px] bg-white rounded-xl shadow-2xl flex flex-col overflow-hidden border border-gray-200"
        style="display: none; z-index: 9999;"
        @click.away="open = false">

        <!-- Chat Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">Kitabasan Assistant</h3>
                    <p class="text-xs text-blue-100">We're here to help!</p>
                </div>
            </div>
            <button @click="open = false" class="text-white hover:bg-white/20 rounded-full p-1 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Messages Area -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" id="chatbot-messages">
            <!-- Welcome Message -->
            <template x-if="messages.length === 0">
                <div class="flex flex-col items-center justify-center h-full text-center p-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Hello! 👋</h4>
                    <p class="text-gray-600 text-sm mb-4">I'm your Kitabasan assistant. Ask me anything about our platform, courses, enrollment, or support!</p>
                    <div class="flex flex-wrap gap-2 justify-center">
                        <button @click="sendQuickMessage('What is Kitabasan?')" class="text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded-full hover:bg-blue-100 transition">
                            What is Kitabasan?
                        </button>
                        <button @click="sendQuickMessage('How do I enroll?')" class="text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded-full hover:bg-blue-100 transition">
                            How do I enroll?
                        </button>
                        <button @click="sendQuickMessage('Are there free courses?')" class="text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded-full hover:bg-blue-100 transition">
                            Free courses?
                        </button>
                    </div>
                </div>
            </template>

            <!-- Chat Messages -->
            <template x-for="(message, index) in messages" :key="index">
                <div :class="message.type === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="message.type === 'user' ? 'bg-blue-600 text-white max-w-[80%]' : 'bg-white text-gray-800 max-w-[80%] shadow-sm'"
                         class="rounded-lg px-4 py-2">
                        <p class="text-sm" x-text="message.text"></p>
                        <span class="text-xs opacity-70 mt-1 block" x-text="message.time"></span>
                    </div>
                </div>
            </template>

            <!-- Loading Indicator -->
            <div x-show="loading" class="flex justify-start" style="display: none;">
                <div class="bg-white text-gray-800 rounded-lg px-4 py-2 shadow-sm">
                    <div class="flex items-center gap-2">
                        <div class="flex gap-1">
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-white border-t border-gray-200">
            <form @submit.prevent="sendMessage" class="flex gap-2">
                <input
                    type="text"
                    x-model="messageInput"
                    placeholder="Type your message..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                    required
                    autocomplete="off">
                <button
                    type="submit"
                    :disabled="loading || !messageInput"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition flex items-center justify-center">
                    <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    <svg x-show="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

</div>
