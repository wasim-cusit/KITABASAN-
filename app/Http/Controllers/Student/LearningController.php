<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Lesson;
use App\Models\Topic;
use App\Models\LessonProgress;
use App\Models\CourseEnrollment;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    public function index($bookId)
    {
        $book = Book::with(['chapters.lessons.topics'])->findOrFail($bookId);
        $user = Auth::user();

        // Check enrollment
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();

        // If course is not free and user is not enrolled, they can still see free chapters
        $chapters = $book->chapters()
            ->with(['lessons' => function($query) use ($enrollment, $book) {
                $query->orderBy('order');
                if (!$book->is_free && !$enrollment) {
                    // Only show free lessons if not enrolled
                    $query->where('is_free', true);
                }
            }])
            ->orderBy('order')
            ->get();

        return view('student.learning.index', compact('book', 'chapters', 'enrollment'));
    }

    public function show($bookId, $lessonId)
    {
        $book = Book::findOrFail($bookId);
        $lesson = Lesson::with(['chapter', 'topics', 'quizzes'])->findOrFail($lessonId);
        $user = Auth::user();

        // Check enrollment
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();

        // Check if lesson is accessible
        $isFreeLesson = $lesson->is_free || $lesson->chapter->is_free;
        $isFreeCourse = $book->is_free;

        // Allow access if: course is free OR user is enrolled OR lesson/chapter is free
        if (!$isFreeCourse && !$enrollment && !$isFreeLesson) {
            return redirect()->route('student.courses.show', $bookId)
                ->with('error', 'You need to purchase this course to access this lesson.');
        }

        // Filter topics - only show free topics if not enrolled
        if (!$isFreeCourse && !$enrollment) {
            $lesson->topics = $lesson->topics->filter(function($topic) {
                return $topic->is_free;
            });
        }

        $chapters = $book->chapters()->with(['lessons'])->orderBy('order')->get();

        return view('student.learning.show', compact('book', 'lesson', 'chapters', 'enrollment', 'videoService'));
    }

    public function showTopic($bookId, $lessonId, $topicId)
    {
        $book = Book::findOrFail($bookId);
        $lesson = Lesson::findOrFail($lessonId);
        $topic = Topic::findOrFail($topicId);
        $user = Auth::user();

        // Check enrollment
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();

        // Check if topic is accessible
        $isFreeTopic = $topic->is_free || $lesson->is_free || $lesson->chapter->is_free;
        $isFreeCourse = $book->is_free;

        // Allow access if: course is free OR user is enrolled OR topic/lesson/chapter is free
        if (!$isFreeCourse && !$enrollment && !$isFreeTopic) {
            return redirect()->route('student.learning.lesson', ['bookId' => $bookId, 'lessonId' => $lessonId])
                ->with('error', 'You need to purchase this course to access this topic.');
        }

        $chapters = $book->chapters()->with(['lessons'])->orderBy('order')->get();

        return view('student.learning.topic', compact('book', 'lesson', 'topic', 'chapters', 'enrollment', 'videoService'));
    }

    public function updateProgress(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'watch_percentage' => 'nullable|integer|min:0|max:100',
            'last_watched_position' => 'nullable|integer|min:0',
            'is_completed' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $lesson = Lesson::findOrFail($request->lesson_id);
        $book = $lesson->chapter->book;

        // Check enrollment
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'active')
            ->first();

        // Check if lesson is free
        $isFreeLesson = $lesson->is_free || $lesson->chapter->is_free;
        $isFreeCourse = $book->is_free;

        // Allow progress tracking if: course is free OR user is enrolled OR lesson is free
        if (!$isFreeCourse && !$enrollment && !$isFreeLesson) {
            return response()->json(['error' => 'Not enrolled'], 403);
        }

        $progress = LessonProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'watch_percentage' => $request->watch_percentage ?? 0,
                'last_watched_position' => $request->last_watched_position ?? 0,
                'last_watched_at' => now(),
                'is_completed' => $request->is_completed ?? false,
                'completed_at' => $request->is_completed ? now() : null,
            ]
        );

        // Update course enrollment progress
        if ($enrollment) {
            $this->updateCourseProgress($enrollment);
        }

        return response()->json(['success' => true, 'progress' => $progress]);
    }

    private function updateCourseProgress($enrollment)
    {
        $book = $enrollment->book;
        $user = $enrollment->user;

        $totalLessons = $book->chapters()->withCount('lessons')->get()->sum('lessons_count');
        $completedLessons = LessonProgress::where('user_id', $user->id)
            ->whereHas('lesson.chapter', function($query) use ($book) {
                $query->where('book_id', $book->id);
            })
            ->where('is_completed', true)
            ->count();

        $progressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        $enrollment->update([
            'progress_percentage' => $progressPercentage,
            'last_accessed_at' => now(),
        ]);
    }
}
