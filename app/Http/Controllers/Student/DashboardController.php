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
            ->with(['book.subject'])
            ->latest()
            ->get();

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

        return view('student.dashboard.index', compact('enrollments', 'recentProgress', 'stats'));
    }
}
