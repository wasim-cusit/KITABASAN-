<?php

namespace App\Services;

use App\Models\ChatbotConversation;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatbotService
{
    private $knowledgeBase = [];

    public function __construct()
    {
        $this->loadKnowledgeBase();
    }

    /**
     * Load knowledge base from data file
     */
    private function loadKnowledgeBase()
    {
        $knowledgeFile = storage_path('app/public/chatbot/knowledge_base.json');

        if (file_exists($knowledgeFile)) {
            $this->knowledgeBase = json_decode(file_get_contents($knowledgeFile), true) ?? [];
        } else {
            $this->knowledgeBase = [];
        }
    }

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

            Log::error('Chatbot Error: ' . $e->getMessage());
            return 'Sorry, I encountered an error. Please try again later.';
        }
    }

    /**
     * Get AI response from knowledge base
     */
    private function getAIResponse(User $user, string $message, string $userType): string
    {
        $message = trim(strtolower($message));

        if (empty($message)) {
            return "I'm here to help! Please ask me anything about KITAB ASAN or your learning journey.";
        }

        $context = $this->buildContext($user, $userType);

        // Check for greetings
        foreach ($this->knowledgeBase['greetings'] ?? [] as $greeting => $response) {
            if (Str::contains($message, $greeting)) {
                return $response;
            }
        }

        // Check for goodbyes
        foreach ($this->knowledgeBase['goodbyes'] ?? [] as $goodbye => $response) {
            if (Str::contains($message, $goodbye)) {
                return $response;
            }
        }

        // Check role-specific FAQs first
        if ($userType === 'student') {
            foreach ($this->knowledgeBase['student_faqs'] ?? [] as $faq) {
                $questions = $faq['question'] ?? [];
                foreach ($questions as $question) {
                    if (Str::contains($message, $question) || $this->calculateSimilarity($message, $question) > 0.6) {
                        return $this->personalizeResponse($faq['answer'], $context);
                    }
                }
            }
        }

        if ($userType === 'teacher') {
            foreach ($this->knowledgeBase['teacher_faqs'] ?? [] as $faq) {
                $questions = $faq['question'] ?? [];
                foreach ($questions as $question) {
                    if (Str::contains($message, $question) || $this->calculateSimilarity($message, $question) > 0.6) {
                        return $this->personalizeResponse($faq['answer'], $context);
                    }
                }
            }
        }

        // Check general FAQs
        foreach ($this->knowledgeBase['faqs'] ?? [] as $faq) {
            $questions = $faq['question'] ?? [];
            foreach ($questions as $question) {
                if (Str::contains($message, $question) || $this->calculateSimilarity($message, $question) > 0.6) {
                    return $this->personalizeResponse($faq['answer'], $context);
                }
            }
        }

        // Contextual responses based on user data
        if ($userType === 'student') {
            if (stripos($message, 'progress') !== false || stripos($message, 'how am i doing') !== false) {
                if ($context['enrollments'] > 0) {
                    return "You're enrolled in {$context['enrollments']} course(s) and have completed {$context['completed_courses']} course(s). You can check detailed progress in your student dashboard. Keep up the great work!";
                }
                return "You haven't enrolled in any courses yet. Browse our courses page to find courses that interest you and start your learning journey!";
            }

            if (stripos($message, 'enroll') !== false && $context['enrollments'] > 0) {
                return "You're currently enrolled in {$context['enrollments']} course(s). To enroll in more courses, browse our courses page and click 'Enroll Now' on any course that interests you!";
            }
        }

        if ($userType === 'teacher') {
            if (stripos($message, 'course') !== false && stripos($message, 'create') !== false) {
                if ($context['courses'] > 0) {
                    return "You've created {$context['courses']} course(s) with {$context['students']} total student enrollments. To create a new course, go to your teacher dashboard and click 'Create New Course'. Fill in all the details, add your content, and publish!";
                }
                return "To create your first course, go to your teacher dashboard and click 'Create New Course'. You'll need to provide course details, create modules and chapters, add lessons with videos or quizzes, and then publish your course!";
            }

            if (stripos($message, 'students') !== false && $context['students'] > 0) {
                return "You have {$context['students']} students enrolled across your {$context['courses']} course(s). View detailed student progress, analytics, and engagement metrics from your teacher dashboard!";
            }
        }

        // Fallback response
        $fallbackMessages = [
            "I understand you're asking about \"" . htmlspecialchars($message) . "\". While I don't have a specific answer for that right now, I can help you with questions about courses, enrollment, payments, progress tracking, or platform features. What would you like to know?",
            "That's an interesting question! I can help you with information about our courses, learning resources, enrollment process, payments, certificates, or platform features. What specific topic would you like to learn more about?",
            "Thanks for your question! I'm here to help with course-related queries, enrollment, payments, progress tracking, certificates, and general platform information. How can I assist you?"
        ];

        return $fallbackMessages[array_rand($fallbackMessages)];
    }

    /**
     * Personalize response with user context
     */
    private function personalizeResponse(string $answer, array $context): string
    {
        // Replace placeholders with actual user data if needed
        if (isset($context['user_name'])) {
            // Could personalize with user name if desired
        }

        return $answer;
    }

    /**
     * Calculate similarity between two strings
     */
    private function calculateSimilarity(string $str1, string $str2): float
    {
        similar_text($str1, $str2, $percent);
        return $percent / 100;
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

