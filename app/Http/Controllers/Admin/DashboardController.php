<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\Payment;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_teachers' => User::role('teacher')->count(),
            'total_students' => User::role('student')->count(),
            'total_courses' => Book::count(),
            'pending_courses' => Book::where('status', 'draft')->count(),
            'total_enrollments' => CourseEnrollment::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount') ?? 0,
            'pending_payments' => Payment::where('status', 'pending')->count(),
        ];

        $recentPayments = Payment::with(['user', 'book'])
            ->latest()
            ->limit(10)
            ->get();

        $pendingCourses = Book::with(['teacher', 'subject'])
            ->where('status', 'pending')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'recentPayments', 'pendingCourses'));
    }
}
