<?php

namespace App\Services;

use Illuminate\Support\Str;

class PublicChatbotService
{
    /**
     * Knowledge base data - stored in easy-to-edit format
     * This can be moved to database or JSON file later for easy updates
     */
    private $knowledgeBase = [];

    public function __construct()
    {
        $this->loadKnowledgeBase();
    }

    /**
     * Load knowledge base from data file
     * For now, we'll use inline data, but this can be easily changed to load from JSON/DB
     */
    private function loadKnowledgeBase()
    {
        $knowledgeFile = storage_path('app/public/chatbot/knowledge_base.json');

        if (file_exists($knowledgeFile)) {
            $this->knowledgeBase = json_decode(file_get_contents($knowledgeFile), true) ?? [];
        } else {
            // Default knowledge base
            $this->knowledgeBase = $this->getDefaultKnowledgeBase();
            // Create directory if it doesn't exist
            if (!is_dir(dirname($knowledgeFile))) {
                mkdir(dirname($knowledgeFile), 0755, true);
            }
            // Save default knowledge base
            file_put_contents($knowledgeFile, json_encode($this->knowledgeBase, JSON_PRETTY_PRINT));
        }
    }

    /**
     * Get default knowledge base about the platform
     */
    private function getDefaultKnowledgeBase(): array
    {
        return [
            'platform_info' => [
                'name' => 'Kitabasan Learning Platform',
                'description' => 'An online learning platform offering quality courses from expert instructors',
                'features' => [
                    'Online courses with video lessons',
                    'Free and paid course options',
                    'Expert instructors',
                    'Flexible learning schedule',
                    'Course progress tracking',
                    'Certificates upon completion'
                ]
            ],
            'faqs' => [
                [
                    'question' => ['what is kitabasan', 'what is this platform', 'tell me about kitabasan'],
                    'answer' => 'Kitabasan Learning Platform is an online education platform that offers quality courses taught by expert instructors. We provide both free and paid courses across various subjects and grade levels. You can learn at your own pace and track your progress.'
                ],
                [
                    'question' => ['how do i enroll', 'how to enroll', 'enrollment process', 'join course'],
                    'answer' => 'To enroll in a course, simply browse our courses, select the one you\'re interested in, and click "Enroll Now". For free courses, you can start learning immediately. For paid courses, you\'ll need to complete the payment process first. Once enrolled, you can access all course materials and track your progress.'
                ],
                [
                    'question' => ['free courses', 'are there free courses', 'free classes'],
                    'answer' => 'Yes! We offer many free courses. You can browse our free courses section on the homepage or filter courses by "Free" when browsing. Free courses provide the same high-quality content as paid courses.'
                ],
                [
                    'question' => ['payment', 'how to pay', 'payment methods', 'pay for course'],
                    'answer' => 'We accept multiple payment methods including credit/debit cards, bank transfers, and mobile banking. Payment methods can be configured by the admin. All payments are secure and processed through trusted payment gateways.'
                ],
                [
                    'question' => ['contact', 'how to contact', 'support', 'help', 'customer service'],
                    'answer' => 'You can contact us through our Contact page. We also offer WhatsApp support at +92 334 2372772. Our support team is available to help with any questions about courses, enrollment, payments, or technical issues. You can also email us at info@kitabasan.com.'
                ],
                [
                    'question' => ['about us', 'who are we', 'company information'],
                    'answer' => 'Kitabasan Learning Platform is dedicated to providing quality online education. We believe in making learning accessible to everyone through our comprehensive course library and expert instructors. Visit our About Us page to learn more about our mission and values.'
                ],
                [
                    'question' => ['courses available', 'what courses', 'subjects', 'what can i learn'],
                    'answer' => 'We offer courses across multiple subjects and grade levels. You can browse our courses page to see all available courses. Our courses cover various subjects including mathematics, science, languages, technology, and more. Each course includes video lessons, materials, and assessments.'
                ],
                [
                    'question' => ['certificate', 'do i get certificate', 'certification'],
                    'answer' => 'Yes! Many of our courses offer certificates upon completion. Certificate availability depends on the specific course. Check the course details page to see if a certificate is offered. To earn a certificate, you typically need to complete all lessons and pass assessments.'
                ],
                [
                    'question' => ['system requirements', 'what do i need', 'device requirements'],
                    'answer' => 'You can access our platform from any device with an internet connection - desktop, laptop, tablet, or mobile phone. We recommend using a modern web browser like Chrome, Firefox, Safari, or Edge for the best experience. A stable internet connection is required for video lessons.'
                ],
                [
                    'question' => ['developer', 'who developed this', 'created by', 'contact developer'],
                    'answer' => 'This platform was developed by MUHAMMAD WASIM. You can contact the developer via phone at +92 334 2372772 or through WhatsApp for technical inquiries or platform-related questions.'
                ]
            ],
            'greetings' => [
                'hi' => 'Hello! Welcome to Kitabasan Learning Platform. How can I help you today?',
                'hello' => 'Hello! I\'m here to help you with any questions about our platform. What would you like to know?',
                'hey' => 'Hey there! Welcome to Kitabasan. How can I assist you?',
                'good morning' => 'Good morning! Welcome to Kitabasan Learning Platform. How can I help you today?',
                'good afternoon' => 'Good afternoon! Welcome to Kitabasan. What would you like to know?',
                'good evening' => 'Good evening! Welcome to Kitabasan Learning Platform. How can I assist you?'
            ],
            'goodbyes' => [
                'thank you' => 'You\'re welcome! If you have any more questions, feel free to ask. Happy learning!',
                'thanks' => 'You\'re welcome! Feel free to ask if you need anything else. Enjoy your learning journey!',
                'bye' => 'Goodbye! Have a great day and happy learning!',
                'goodbye' => 'Goodbye! We hope to see you again soon. Happy learning!'
            ]
        ];
    }

    /**
     * Process user message and return response
     */
    public function processMessage(string $message, ?string $sessionId = null): array
    {
        $message = trim(strtolower($message));

        if (empty($message)) {
            return [
                'response' => 'I\'m here to help! Please ask me anything about Kitabasan Learning Platform.',
                'type' => 'info'
            ];
        }

        // Check for greetings
        foreach ($this->knowledgeBase['greetings'] ?? [] as $greeting => $response) {
            if (Str::contains($message, $greeting)) {
                return [
                    'response' => $response,
                    'type' => 'greeting'
                ];
            }
        }

        // Check for goodbyes
        foreach ($this->knowledgeBase['goodbyes'] ?? [] as $goodbye => $response) {
            if (Str::contains($message, $goodbye)) {
                return [
                    'response' => $response,
                    'type' => 'goodbye'
                ];
            }
        }

        // Check FAQs
        foreach ($this->knowledgeBase['faqs'] ?? [] as $faq) {
            $questions = $faq['question'] ?? [];
            foreach ($questions as $question) {
                if (Str::contains($message, $question) || $this->calculateSimilarity($message, $question) > 0.6) {
                    return [
                        'response' => $faq['answer'],
                        'type' => 'faq'
                    ];
                }
            }
        }

        // Fallback response
        return [
            'response' => 'I understand you\'re asking about "' . htmlspecialchars($message) . '". While I don\'t have a specific answer for that right now, I can help you with questions about our courses, enrollment, payments, platform features, or contact information. What would you like to know?',
            'type' => 'fallback'
        ];
    }

    /**
     * Calculate similarity between two strings (simple implementation)
     */
    private function calculateSimilarity(string $str1, string $str2): float
    {
        similar_text($str1, $str2, $percent);
        return $percent / 100;
    }

    /**
     * Get knowledge base for admin editing
     */
    public function getKnowledgeBase(): array
    {
        return $this->knowledgeBase;
    }

    /**
     * Update knowledge base (for admin panel later)
     */
    public function updateKnowledgeBase(array $knowledgeBase): bool
    {
        $knowledgeFile = storage_path('app/public/chatbot/knowledge_base.json');
        $this->knowledgeBase = $knowledgeBase;

        return file_put_contents($knowledgeFile, json_encode($knowledgeBase, JSON_PRETTY_PRINT)) !== false;
    }
}
