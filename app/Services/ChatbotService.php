<?php

namespace App\Services;

use App\Models\ChatbotConversation;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class ChatbotService
{
    /**
     * Process chatbot message
     */
    public function processMessage(User $user, string $message, string $userType = 'student'): string
    {
        // Create conversation record
        $conversation = ChatbotConversation::create([
            'user_id' => $user->id,
            'user_type' => $userType,
            'message' => $message,
            'status' => 'pending',
        ]);

        try {
            // Get AI response
            $response = $this->getAIResponse($user, $message, $userType);

            // Update conversation
            $conversation->update([
                'response' => $response,
                'status' => 'responded',
            ]);

            return $response;
        } catch (\Exception $e) {
            $conversation->update([
                'response' => 'Sorry, I encountered an error. Please try again later.',
                'status' => 'failed',
            ]);

            \Log::error('Chatbot Error: ' . $e->getMessage());
            return 'Sorry, I encountered an error. Please try again later.';
        }
    }

    /**
     * Get AI response from API
     */
    private function getAIResponse(User $user, string $message, string $userType): string
    {
        // TODO: Integrate with AI service (OpenAI, Anthropic, etc.)
        // For now, return a simple response

        $context = $this->buildContext($user, $userType);

        // Example: Simple rule-based responses
        if (stripos($message, 'course') !== false) {
            return "I can help you with course-related questions. What would you like to know?";
        }

        if (stripos($message, 'payment') !== false) {
            return "For payment-related queries, please contact our support team or check your payment history in the dashboard.";
        }

        if (stripos($message, 'progress') !== false) {
            return "You can check your learning progress in the student dashboard. Would you like me to guide you there?";
        }

        return "Thank you for your message. I'm here to help you with your learning journey. How can I assist you today?";
    }

    /**
     * Build context for AI
     */
    private function buildContext(User $user, string $userType): array
    {
        $context = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_type' => $userType,
        ];

        if ($userType === 'student') {
            $context['enrollments'] = $user->enrollments()->count();
            $context['completed_courses'] = $user->enrollments()
                ->where('progress_percentage', 100)
                ->count();
        }

        if ($userType === 'teacher') {
            $context['courses'] = $user->createdCourses()->count();
            $context['students'] = $user->createdCourses()
                ->withCount('enrollments')
                ->get()
                ->sum('enrollments_count');
        }

        return $context;
    }

    /**
     * Get conversation history
     */
    public function getConversationHistory(User $user, int $limit = 20)
    {
        return ChatbotConversation::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }
}

