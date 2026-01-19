<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $enrollments = CourseEnrollment::where('user_id', $user->id)
            ->where('status', 'active')
            ->with([
                'book.subject',
                'book.teacher.teacherProfile',
                'book.chapters.lessons' => function($query) {
                    $query->orderBy('order');
                }
            ])
            ->latest()
            ->get();

        // Load lesson progress for all enrolled courses
        $courseIds = $enrollments->pluck('book_id');
        $lessonProgress = LessonProgress::where('user_id', $user->id)
            ->whereHas('lesson.chapter', function($query) use ($courseIds) {
                $query->whereIn('book_id', $courseIds);
            })
            ->with(['lesson.chapter'])
            ->get();

        // Attach progress data to each enrollment
        foreach ($enrollments as $enrollment) {
            $enrollment->lessonProgress = $lessonProgress->filter(function($progress) use ($enrollment) {
                return $progress->lesson && $progress->lesson->chapter && $progress->lesson->chapter->book_id == $enrollment->book_id;
            });
        }

        $recentProgress = LessonProgress::where('user_id', $user->id)
            ->with(['lesson.chapter.book'])
            ->latest('last_watched_at')
            ->limit(5)
            ->get();

        $stats = [
            'enrolled_courses' => $enrollments->count(),
            'completed_courses' => $enrollments->where('progress_percentage', 100)->count(),
            'in_progress_courses' => $enrollments->where('progress_percentage', '>', 0)
                ->where('progress_percentage', '<', 100)
                ->count(),
        ];

        return view('student.dashboard.index', compact('enrollments', 'recentProgress', 'stats', 'lessonProgress'));
    }
}
