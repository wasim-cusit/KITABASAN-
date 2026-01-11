@extends('layouts.teacher')

@section('title', 'Chatbot')
@section('page-title', 'AI Assistant')


@section('content')

<div class="bg-white rounded-lg shadow h-[calc(100vh-12rem)] flex flex-col"
     data-initial-messages="{!! htmlspecialchars(json_encode($conversations->map(function($c) {
         $messages = [];
         $messages[] = ['text' => $c->message, 'type' => 'user', 'time' => $c->created_at->format('h:i A')];
         if ($c->response) {
             $messages[] = ['text' => $c->response, 'type' => 'bot', 'time' => $c->updated_at->format('h:i A')];
         }
         return $messages;
     })->flatten(1)), ENT_QUOTES, 'UTF-8') !!}"
     x-data="{
         messages: [],
         loading: false,
         messageInput: '',
         chatbotUrl: '{{ route('teacher.chatbot.send') }}',
         csrfToken: '{{ csrf_token() }}',
         init() {
             const container = this.$el;
             if (container && container.dataset.initialMessages) {
                 try {
                     this.messages = JSON.parse(container.dataset.initialMessages);
                 } catch (e) {
                     console.error('Error parsing messages:', e);
                     this.messages = [];
                 }
             }
             this.$watch('messages', () => {
                 this.scrollToBottom();
             });
             this.scrollToBottom();
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
                     body: JSON.stringify({ message: userMessage })
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
                 this.addMessage('Sorry, I encountered an error. Please try again.', 'bot');
             }
         },
         sendQuickMessage(message) {
             this.messageInput = message;
             this.sendMessage();
         },
         addMessage(text, type) {
             const now = new Date();
             const time = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
             this.messages.push({
                 text: text,
                 type: type,
                 time: time
             });
             this.scrollToBottom();
         }
     }">
    <!-- Chatbot Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-4 rounded-t-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">AI Assistant</h3>
                    <p class="text-xs text-blue-100">Ask me anything about your courses and teaching</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages Area -->
    <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" id="chatbot-messages">
        <!-- Welcome Message -->
        <template x-if="messages.length === 0">
            <div class="flex flex-col items-center justify-center h-full text-center p-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                        </path>
                    </svg>
                </div>
                <h4 class="text-lg font-semibold text-gray-900 mb-2">Hello! 👋</h4>
                <p class="text-gray-600 text-sm mb-4">I'm your teaching assistant. Ask me anything about courses, students, enrollments, or teaching support!</p>
                <div class="flex flex-wrap gap-2 justify-center">
                    <button @click="sendQuickMessage('How do I create a course?')"
                        class="text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded-full hover:bg-blue-100 transition">
                        How to create a course?
                    </button>
                    <button @click="sendQuickMessage('How do I manage students?')"
                        class="text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded-full hover:bg-blue-100 transition">
                        Manage students
                    </button>
                    <button @click="sendQuickMessage('How do I track enrollments?')"
                        class="text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded-full hover:bg-blue-100 transition">
                        Track enrollments
                    </button>
                </div>
            </div>
        </template>

        <!-- All Messages -->
        <template x-for="(message, index) in messages" :key="index">
            <div :class="message.type === 'user' ? 'flex justify-end' : 'flex justify-start'">
                <div :class="message.type === 'user' ? 'bg-blue-600 text-white' : 'bg-white text-gray-800 shadow-sm'"
                    class="rounded-lg px-4 py-2 max-w-[80%]">
                    <p class="text-sm" x-text="message.text"></p>
                    <span class="text-xs opacity-70 mt-1 block" x-text="message.time"></span>
                </div>
            </div>
        </template>

        <!-- Loading Indicator -->
        <div x-show="loading" class="flex justify-start" style="display: none;">
            <div class="bg-white text-gray-800 shadow-sm rounded-lg px-4 py-2">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Input Area -->
    <div class="border-t border-gray-200 p-4 bg-white rounded-b-lg">
        <form @submit.prevent="sendMessage()" class="flex items-center space-x-2">
            <input
                type="text"
                x-model="messageInput"
                placeholder="Type your message..."
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :disabled="loading"
            >
            <button
                type="submit"
                :disabled="loading || !messageInput.trim()"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                <span>Send</span>
            </button>
        </form>
    </div>
</div>
@endsection
