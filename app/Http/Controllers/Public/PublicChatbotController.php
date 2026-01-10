<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicChatbotService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PublicChatbotController extends Controller
{
    protected $chatbotService;

    public function __construct(PublicChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Handle chatbot message
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'session_id' => 'nullable|string|max:255',
        ]);

        try {
            $result = $this->chatbotService->processMessage(
                $request->input('message'),
                $request->input('session_id')
            );

            return response()->json([
                'success' => true,
                'response' => $result['response'],
                'type' => $result['type'] ?? 'general',
            ]);
        } catch (\Exception $e) {
            \Log::error('Public Chatbot Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'response' => 'Sorry, I encountered an error. Please try again later or contact our support team.',
                'type' => 'error',
            ], 500);
        }
    }

    /**
     * Get knowledge base (for admin editing later)
     */
    public function getKnowledgeBase(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->chatbotService->getKnowledgeBase(),
        ]);
    }
}
