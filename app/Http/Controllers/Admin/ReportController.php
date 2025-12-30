<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\Payment;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Date range defaults
        $dateFrom = $request->date_from ?? Carbon::now()->subMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? Carbon::now()->format('Y-m-d');

        // Revenue Report
        $revenueData = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Enrollment Report
        $enrollmentData = CourseEnrollment::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // User Registration Report
        $userRegistrationData = User::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Course Statistics
        $courseStats = [
            'total' => Book::count(),
            'published' => Book::where('status', 'published')->count(),
            'draft' => Book::where('status', 'draft')->count(),
            'free' => Book::where('is_free', true)->count(),
            'paid' => Book::where('is_free', false)->count(),
        ];

        // Top Courses by Enrollment
        $topCourses = Book::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->limit(10)
            ->get();

        // Top Courses by Revenue
        $topRevenueCourses = Book::with(['payments' => function($q) {
                $q->where('status', 'completed');
            }])
            ->get()
            ->map(function($book) {
                $book->total_revenue = $book->payments->sum('amount');
                return $book;
            })
            ->sortByDesc('total_revenue')
            ->take(10);

        // User Statistics
        $userStats = [
            'total' => User::count(),
            'teachers' => User::role('teacher')->count(),
            'students' => User::role('student')->count(),
            'admins' => User::role('admin')->count(),
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
        ];

        // Payment Statistics
        $paymentStats = [
            'total' => Payment::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'completed' => Payment::where('status', 'completed')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->sum('amount'),
            'pending' => Payment::where('status', 'pending')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count(),
            'failed' => Payment::where('status', 'failed')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count(),
        ];

        return view('admin.reports.index', compact(
            'revenueData',
            'enrollmentData',
            'userRegistrationData',
            'courseStats',
            'topCourses',
            'topRevenueCourses',
            'userStats',
            'paymentStats',
            'dateFrom',
            'dateTo'
        ));
    }
}
