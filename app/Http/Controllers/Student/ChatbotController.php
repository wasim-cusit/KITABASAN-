<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    protected $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Show the chatbot interface
     */
    public function index()
    {
        $conversations = $this->chatbotService->getConversationHistory(Auth::user(), 50);
        return view('student.chatbot.index', compact('conversations'));
    }

    /**
     * Send a message to the chatbot
     */
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        try {
            $response = $this->chatbotService->processMessage(
                Auth::user(),
                $request->input('message'),
                'student'
            );

            return response()->json([
                'success' => true,
                'response' => $response,
            ]);
        } catch (\Exception $e) {
            \Log::error('Student Chatbot Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'response' => 'Sorry, I encountered an error. Please try again later.',
            ], 500);
        }
    }
}
