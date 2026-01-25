@extends('layouts.teacher')

@section('title', 'Chatbot')
@section('page-title', 'AI Assistant')


@section('content')

<div class="teacher-chatbot-root"
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
    <div class="teacher-chatbot-header">
        <div class="teacher-chatbot-header-inner">
            <div class="teacher-chatbot-header-brand">
                <div class="teacher-chatbot-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                        </path>
                    </svg>
                </div>
                <div class="teacher-chatbot-header-text">
                    <h3>AI Assistant</h3>
                    <p>Ask me anything about your courses and teaching</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages Area -->
    <div class="teacher-chatbot-messages" id="chatbot-messages">
        <!-- Welcome Message -->
        <template x-if="messages.length === 0">
            <div class="teacher-chatbot-welcome">
                <div class="teacher-chatbot-welcome-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                        </path>
                    </svg>
                </div>
                <h4>Hello! 👋</h4>
                <p>I'm your teaching assistant. Ask me anything about courses, students, enrollments, or teaching support!</p>
                <div class="teacher-chatbot-quick-wrap">
                    <button type="button" @click="sendQuickMessage('How do I create a course?')" class="teacher-chatbot-quick-btn">
                        How to create a course?
                    </button>
                    <button type="button" @click="sendQuickMessage('How do I manage students?')" class="teacher-chatbot-quick-btn">
                        Manage students
                    </button>
                    <button type="button" @click="sendQuickMessage('How do I track enrollments?')" class="teacher-chatbot-quick-btn">
                        Track enrollments
                    </button>
                </div>
            </div>
        </template>

        <!-- All Messages -->
        <template x-for="(message, index) in messages" :key="index">
            <div :class="'teacher-chatbot-msg teacher-chatbot-msg--' + message.type">
                <div :class="'teacher-chatbot-bubble teacher-chatbot-bubble--' + message.type">
                    <p x-text="message.text"></p>
                    <span class="teacher-chatbot-bubble-time" x-text="message.time"></span>
                </div>
            </div>
        </template>

        <!-- Loading Indicator -->
        <div x-show="loading" class="teacher-chatbot-loading">
            <div class="teacher-chatbot-loading-dots">
                <div class="teacher-chatbot-dot"></div>
                <div class="teacher-chatbot-dot"></div>
                <div class="teacher-chatbot-dot"></div>
            </div>
        </div>
    </div>

    <!-- Input Area -->
    <div class="teacher-chatbot-input-wrap">
        <form @submit.prevent="sendMessage()" class="teacher-chatbot-input-form">
            <input
                type="text"
                x-model="messageInput"
                placeholder="Type your message..."
                class="teacher-chatbot-input"
                :disabled="loading"
            >
            <button
                type="submit"
                :disabled="loading || !messageInput.trim()"
                class="teacher-chatbot-send-btn"
            >
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                <span>Send</span>
            </button>
        </form>
    </div>
</div>
@endsection
