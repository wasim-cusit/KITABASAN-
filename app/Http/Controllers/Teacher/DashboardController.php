<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_courses' => Book::where('teacher_id', $user->id)->count(),
            'total_students' => CourseEnrollment::whereHas('book', function($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })->distinct('user_id')->count(),
            'total_enrollments' => CourseEnrollment::whereHas('book', function($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })->count(),
            'pending_courses' => Book::where('teacher_id', $user->id)
                ->where('status', 'pending')
                ->count(),
        ];

        $myCourses = Book::where('teacher_id', $user->id)
            ->with(['subject', 'enrollments'])
            ->latest()
            ->limit(10)
            ->get();

        return view('teacher.dashboard.index', compact('stats', 'myCourses'));
    }
}
