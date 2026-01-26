<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\Payment;
use App\Models\CourseEnrollment;
use App\Models\DeviceBinding;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Date range (used across analytics + revenue stat)
        $stats = [
            'total_users' => User::count(),
            'total_teachers' => User::role('teacher')->count(),
            'total_students' => User::role('student')->count(),
            'total_courses' => Book::count(),
            'pending_courses' => Book::where('status', 'draft')->count(),
            'total_enrollments' => CourseEnrollment::count(),
            // Will be overridden below to respect selected range.
            'total_revenue' => 0,
            'total_revenue_all_time' => Payment::where('status', 'completed')->sum('amount') ?? 0,
            'pending_payments' => Payment::where('status', 'pending')->count(),
        ];

        $rangeMaxDays = 365;
        $fromInput = $request->query('from');
        $toInput = $request->query('to');

        try {
            $from = $fromInput ? Carbon::parse($fromInput)->startOfDay() : Carbon::now()->subDays(29)->startOfDay();
        } catch (\Throwable $e) {
            $from = Carbon::now()->subDays(29)->startOfDay();
        }

        try {
            $to = $toInput ? Carbon::parse($toInput)->endOfDay() : Carbon::now()->endOfDay();
        } catch (\Throwable $e) {
            $to = Carbon::now()->endOfDay();
        }

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        if ($from->diffInDays($to) > $rangeMaxDays) {
            $from = $to->copy()->subDays($rangeMaxDays)->startOfDay();
        }

        $stats['total_revenue'] = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$from, $to])
            ->sum('amount') ?? 0;

        $analyticsRange = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'from_label' => $from->format('M d, Y'),
            'to_label' => $to->format('M d, Y'),
        ];

        $recentPayments = Payment::with(['user', 'book'])
            ->latest()
            ->limit(5)
            ->get();

        $pendingCourses = Book::with(['teacher', 'subject'])
                ->where('status', 'draft')
            ->latest()
            ->limit(5)
            ->get();

        $pendingDeviceResets = \App\Models\DeviceBinding::with('user')
            ->where('status', 'pending_reset')
            ->latest()
            ->limit(5)
            ->get();

        // Analytics
        $dailyRevenueRows = Payment::query()
            ->where('status', 'completed')
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as day, COALESCE(SUM(amount), 0) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $revenueLabels = [];
        $revenueTotals = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $day = $cursor->toDateString();
            $revenueLabels[] = $cursor->format('M d');
            $revenueTotals[] = (float) (($dailyRevenueRows[$day]->total ?? 0));
            $cursor->addDay();
        }

        $paymentStatusRows = Payment::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        $preferredPaymentStatusOrder = ['completed', 'pending', 'failed', 'cancelled'];
        $paymentStatusLabels = array_values(array_unique(array_merge(
            $preferredPaymentStatusOrder,
            $paymentStatusRows->keys()->all()
        )));
        $paymentStatusData = array_map(
            fn ($status) => (int) ($paymentStatusRows[$status] ?? 0),
            $paymentStatusLabels
        );

        $courseStatusRows = Book::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        $preferredCourseStatusOrder = ['published', 'approved', 'pending', 'draft', 'rejected'];
        $courseStatusLabels = array_values(array_unique(array_merge(
            $preferredCourseStatusOrder,
            $courseStatusRows->keys()->all()
        )));
        $courseStatusData = array_map(
            fn ($status) => (int) ($courseStatusRows[$status] ?? 0),
            $courseStatusLabels
        );

        // Students activity (within selected date range)
        $totalStudents = User::role('student')->count();
        $activeStudents = User::role('student')
            ->whereNotNull('last_login_at')
            ->whereBetween('last_login_at', [$from, $to])
            ->count();
        $offlineStudents = max(0, $totalStudents - $activeStudents);

        // Device bindings (students)
        $deviceStatusRows = DeviceBinding::query()
            ->whereHas('user', fn ($q) => $q->role('student'))
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        $preferredDeviceStatusOrder = ['active', 'pending_reset', 'blocked'];
        $deviceStatusLabels = array_values(array_unique(array_merge(
            $preferredDeviceStatusOrder,
            $deviceStatusRows->keys()->all()
        )));
        $deviceStatusData = array_map(
            fn ($status) => (int) ($deviceStatusRows[$status] ?? 0),
            $deviceStatusLabels
        );

        $analytics = [
            'range' => $analyticsRange,
            'revenue_last_30_days' => [
                'labels' => $revenueLabels,
                'data' => $revenueTotals,
            ],
            'payments_by_status' => [
                'labels' => $paymentStatusLabels,
                'data' => $paymentStatusData,
            ],
            'courses_by_status' => [
                'labels' => $courseStatusLabels,
                'data' => $courseStatusData,
            ],
            'students_activity' => [
                'labels' => ['Active', 'Offline'],
                'data' => [$activeStudents, $offlineStudents],
            ],
            'student_devices_by_status' => [
                'labels' => $deviceStatusLabels,
                'data' => $deviceStatusData,
            ],
        ];

        return view('admin.dashboard.index', compact('stats', 'recentPayments', 'pendingCourses', 'pendingDeviceResets', 'analytics'));
    }
}
