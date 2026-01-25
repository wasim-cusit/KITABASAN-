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

        // Check enrollment - must have payment_status = 'paid' for paid courses
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->where('status', 'active')
            ->where(function($query) use ($book) {
                // For paid courses, require payment_status = 'paid'
                if (!$book->is_free) {
                    $query->where('payment_status', 'paid');
                }
            })
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();

        // If course is not free and user is not enrolled (with paid status), they can only see free chapters
        $chapters = $book->chapters()
            ->with(['lessons' => function($query) use ($enrollment, $book) {
                $query->orderBy('order')
                      ->with(['topics' => function($q) use ($enrollment, $book) {
                          // Show ALL topics, but access will be controlled in the view
                          $q->orderBy('order');
                      }]);
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
        $book = Book::with(['modules.chapters.lessons', 'chapters.lessons'])->findOrFail($bookId);
        $lesson = Lesson::with(['chapter.module', 'chapter', 'topics', 'quizzes'])->findOrFail($lessonId);
        $user = Auth::user();

        // Check enrollment - must have payment_status = 'paid' for paid courses
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->where('status', 'active')
            ->where(function($query) use ($book) {
                // For paid courses, require payment_status = 'paid'
                if (!$book->is_free) {
                    $query->where('payment_status', 'paid');
                }
            })
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();

        // Check if lesson is accessible
        $isFreeLesson = $lesson->is_free || ($lesson->chapter && $lesson->chapter->is_free);
        $isFreeCourse = $book->is_free;

        // Allow access if: course is free OR user is enrolled OR lesson/chapter is free
        if (!$isFreeCourse && !$enrollment && !$isFreeLesson) {
            return redirect()->route('student.courses.show', $bookId)
                ->with('error', 'You need to purchase this course to access this lesson.');
        }

        // Load all topics (don't filter - show all but control access in view)
        $lesson->load(['topics' => function($query) {
            $query->orderBy('order');
        }]);

        // Load modules with chapters and lessons
        $modules = $book->modules()
            ->with(['chapters' => function($query) use ($enrollment, $book) {
                $query->orderBy('order')
                      ->with(['lessons' => function($q) use ($enrollment, $book) {
                          $q->orderBy('order')
                            ->with(['topics' => function($topicQuery) {
                                $topicQuery->orderBy('order');
                            }]);
                          if (!$book->is_free && !$enrollment) {
                              $q->where('is_free', true);
                          }
                      }]);
            }])
            ->orderBy('order_index')
            ->get();

        // Load all chapters (with or without modules) - include topics
        $chapters = $book->chapters()
            ->with(['lessons' => function($query) use ($enrollment, $book) {
                $query->orderBy('order')
                      ->with(['topics' => function($topicQuery) {
                          $topicQuery->orderBy('order');
                      }]);
                if (!$book->is_free && !$enrollment) {
                    $query->where('is_free', true);
                }
            }])
            ->orderBy('order')
            ->get();

        // If no modules exist, create a default structure
        if ($modules->isEmpty() && $chapters->isNotEmpty()) {
            // Group chapters that don't have modules
            $chaptersWithoutModules = $chapters->whereNull('module_id');
            if ($chaptersWithoutModules->isNotEmpty()) {
                // We'll show these in the sidebar without module grouping
            }
        }

        // Calculate progress - get all lessons
        $allLessons = $chapters->flatMap->lessons;

        // Get all lessons for navigation (previous/next) - sorted by chapter and lesson order
        $allLessonsSorted = $allLessons->sortBy(function($l) {
            return ($l->chapter ? $l->chapter->order : 0) * 1000 + $l->order;
        })->values();

        $currentLessonIndex = $allLessonsSorted->search(function($l) use ($lessonId) {
            return $l->id == $lessonId;
        });

        $previousLesson = $currentLessonIndex !== false && $currentLessonIndex > 0 ? $allLessonsSorted[$currentLessonIndex - 1] : null;
        $nextLesson = $currentLessonIndex !== false && $currentLessonIndex < $allLessonsSorted->count() - 1 ? $allLessonsSorted[$currentLessonIndex + 1] : null;
        $totalLessons = $allLessons->count();
        $completedLessons = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $allLessons->pluck('id'))
            ->where('is_completed', true)
            ->count();

        $progressPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        // Get progress for each lesson
        $lessonProgresses = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $allLessons->pluck('id'))
            ->get()
            ->keyBy('lesson_id');

        $videoService = $this->videoService;

        return view('student.learning.show', compact(
            'book',
            'lesson',
            'modules',
            'chapters',
            'enrollment',
            'videoService',
            'totalLessons',
            'completedLessons',
            'progressPercentage',
            'lessonProgresses',
            'previousLesson',
            'nextLesson'
        ));
    }

    public function showTopic($bookId, $lessonId, $topicId)
    {
        $book = Book::findOrFail($bookId);
        $lesson = Lesson::findOrFail($lessonId);
        $topic = Topic::findOrFail($topicId);
        $user = Auth::user();

        // Check enrollment - must have payment_status = 'paid' for paid courses
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->where('status', 'active')
            ->where(function($query) use ($book) {
                // For paid courses, require payment_status = 'paid'
                if (!$book->is_free) {
                    $query->where('payment_status', 'paid');
                }
            })
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

        $chapters = $book->chapters()
            ->with(['lessons' => function($query) {
                $query->orderBy('order')
                      ->with(['topics' => function($topicQuery) {
                          $topicQuery->orderBy('order');
                      }]);
            }])
            ->orderBy('order')
            ->get();

        // Get all topics for navigation (previous/next)
        $allTopics = $lesson->topics()->orderBy('order')->get();
        $currentTopicIndex = $allTopics->search(function($t) use ($topicId) {
            return $t->id == $topicId;
        });

        $previousTopic = $currentTopicIndex > 0 ? $allTopics[$currentTopicIndex - 1] : null;
        $nextTopic = $currentTopicIndex < $allTopics->count() - 1 ? $allTopics[$currentTopicIndex + 1] : null;

        $videoService = $this->videoService;

        return view('student.learning.topic', compact('book', 'lesson', 'topic', 'chapters', 'enrollment', 'videoService', 'previousTopic', 'nextTopic'));
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

        // Check enrollment - must have payment_status = 'paid' for paid courses
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'active')
            ->where(function($query) use ($book) {
                // For paid courses, require payment_status = 'paid'
                if (!$book->is_free) {
                    $query->where('payment_status', 'paid');
                }
            })
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
