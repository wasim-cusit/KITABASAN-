<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\Mcq;
use App\Models\LessonProgress;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\DB;

class QuizService
{
    /**
     * Submit quiz answers and calculate score
     */
    public function submitQuiz(int $userId, int $quizId, array $answers, ?int $timeTaken = null, ?\DateTime $startedAt = null): QuizSubmission
    {
        $quiz = Quiz::with('mcqs')->findOrFail($quizId);
        
        // Get questions from quiz
        $questions = $quiz->getQuestions();
        
        if (empty($questions)) {
            throw new \Exception('Quiz has no questions');
        }

        $totalQuestions = count($questions);
        $correctAnswers = 0;
        $userAnswers = [];

        // Calculate score
        foreach ($questions as $question) {
            $questionId = $question['id'] ?? null;
            $userAnswer = $answers[$questionId] ?? null;
            $correctAnswer = $question['correct_answer'] ?? null;

            $userAnswers[$questionId] = $userAnswer;

            // Check if answer is correct
            if ($userAnswer !== null && $userAnswer == $correctAnswer) {
                $correctAnswers++;
            }
        }

        // Calculate percentage score
        $score = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
        
        // Check if passed (score >= passing_score, default 70%)
        $passingScore = $quiz->passing_score ?? 70;
        $passed = $score >= $passingScore;

        // Create submission
        $submission = QuizSubmission::create([
            'user_id' => $userId,
            'quiz_id' => $quizId,
            'lesson_id' => $quiz->lesson_id,
            'answers' => $userAnswers,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'score' => round($score, 2),
            'passing_score' => $passingScore,
            'passed' => $passed,
            'time_taken' => $timeTaken,
            'started_at' => $startedAt,
            'submitted_at' => now(),
        ]);

        // Mark lesson as completed if quiz passed and lesson exists
        if ($passed && $quiz->lesson_id) {
            $this->markLessonQuizCompleted($userId, $quiz->lesson_id);
        }

        return $submission;
    }

    /**
     * Mark lesson as completed when quiz is passed (>= 70%)
     */
    protected function markLessonQuizCompleted(int $userId, int $lessonId): void
    {
        // Update lesson progress to mark as completed
        $lessonProgress = LessonProgress::where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->first();

        if ($lessonProgress) {
            $lessonProgress->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        } else {
            LessonProgress::create([
                'user_id' => $userId,
                'lesson_id' => $lessonId,
                'is_completed' => true,
                'completed_at' => now(),
                'watch_percentage' => 100,
            ]);
        }

        // Update course enrollment progress
        $lesson = \App\Models\Lesson::with('chapter.book')->find($lessonId);
        if ($lesson && $lesson->chapter && $lesson->chapter->book) {
            $enrollment = CourseEnrollment::where('user_id', $userId)
                ->where('book_id', $lesson->chapter->book_id)
                ->first();

            if ($enrollment) {
                $this->updateCourseProgress($enrollment);
            }
        }
    }

    /**
     * Update course enrollment progress percentage
     */
    protected function updateCourseProgress($enrollment): void
    {
        $course = $enrollment->book;
        $userId = $enrollment->user_id;

        // Get total lessons count
        $totalLessons = \App\Models\Lesson::whereHas('chapter', function ($query) use ($course) {
            $query->where('book_id', $course->id);
        })->count();

        if ($totalLessons == 0) {
            return;
        }

        // Get completed lessons count
        $completedLessons = LessonProgress::where('user_id', $userId)
            ->where('is_completed', true)
            ->whereHas('lesson.chapter', function ($query) use ($course) {
                $query->where('book_id', $course->id);
            })
            ->count();

        // Calculate progress percentage
        $progressPercentage = ($completedLessons / $totalLessons) * 100;

        $enrollment->update([
            'progress_percentage' => round($progressPercentage, 2),
            'last_accessed_at' => now(),
        ]);
    }

    /**
     * Validate quiz submission data
     */
    public function validateSubmission(int $quizId, array $answers): array
    {
        $errors = [];
        $quiz = Quiz::findOrFail($quizId);

        // Check if quiz is active
        if (!$quiz->is_active) {
            $errors[] = 'This quiz is not active.';
        }

        // Check time limit if set
        if ($quiz->time_limit) {
            // Time limit validation should be done on frontend and passed to backend
        }

        // Check if all questions are answered
        $questions = $quiz->getQuestions();
        foreach ($questions as $question) {
            $questionId = $question['id'] ?? null;
            if ($questionId && !isset($answers[$questionId])) {
                $errors[] = "Question {$questionId} is not answered.";
            }
        }

        return $errors;
    }

    /**
     * Get quiz statistics for a user
     */
    public function getUserQuizStats(int $userId, int $quizId): array
    {
        $quiz = Quiz::findOrFail($quizId);
        
        $submissions = QuizSubmission::where('user_id', $userId)
            ->where('quiz_id', $quizId)
            ->orderBy('submitted_at', 'desc')
            ->get();

        $bestScore = $submissions->max('score') ?? 0;
        $lastScore = $submissions->first()->score ?? 0;
        $attemptsCount = $submissions->count();
        $passedCount = $submissions->where('passed', true)->count();
        $hasPassed = $quiz->hasUserPassed($userId);

        return [
            'best_score' => round($bestScore, 2),
            'last_score' => round($lastScore, 2),
            'attempts_count' => $attemptsCount,
            'passed_count' => $passedCount,
            'has_passed' => $hasPassed,
            'passing_score' => $quiz->passing_score,
            'submissions' => $submissions,
        ];
    }
}
