<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\QuizService;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    protected $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function show($courseId, $chapterId, $lessonId, $quizId)
    {
        $quiz = Quiz::with(['lesson.chapter.book', 'mcqs'])
            ->where('lesson_id', $lessonId)
            ->findOrFail($quizId);

        // Check access
        if (!$quiz->lesson->isAccessible(Auth::user()) && !$quiz->is_preview) {
            return redirect()->route('student.courses.show', $courseId)
                ->with('error', 'You need to enroll in this course to access the quiz.');
        }

        // Get user's previous submissions
        $submissions = $quiz->userSubmissions(Auth::id())->get();
        $stats = $this->quizService->getUserQuizStats(Auth::id(), $quizId);

        return view('student.quizzes.show', compact('quiz', 'submissions', 'stats'));
    }

    public function submit(Request $request, $courseId, $chapterId, $lessonId, $quizId)
    {
        $quiz = Quiz::with(['lesson.chapter.book'])->findOrFail($quizId);

        // Check access
        if (!$quiz->lesson->isAccessible(Auth::user()) && !$quiz->is_preview) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required',
            'time_taken' => 'nullable|integer|min:0',
            'started_at' => 'nullable|date',
        ]);

        // Validate submission
        $errors = $this->quizService->validateSubmission($quizId, $request->answers);
        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        try {
            $submission = $this->quizService->submitQuiz(
                Auth::id(),
                $quizId,
                $request->answers,
                $request->time_taken,
                $request->started_at ? \Carbon\Carbon::parse($request->started_at) : null
            );

            return response()->json([
                'success' => true,
                'submission' => $submission,
                'message' => $submission->getResultMessage(),
                'passed' => $submission->passed,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function results($courseId, $chapterId, $lessonId, $quizId, $submissionId)
    {
        $submission = \App\Models\QuizSubmission::with(['quiz.mcqs'])
            ->where('quiz_id', $quizId)
            ->where('user_id', Auth::id())
            ->findOrFail($submissionId);

        return view('student.quizzes.results', compact('submission'));
    }
}
