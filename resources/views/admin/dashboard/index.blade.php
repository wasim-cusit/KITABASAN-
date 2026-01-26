    @extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="container mx-auto px-0 lg:px-4">

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-6 lg:mb-8" data-testid="admin-dashboard-stats">
        <a href="{{ route('admin.students.index') }}"
           class="rounded-lg border border-blue-200 bg-blue-50/60 p-4 lg:p-6 block hover:shadow-md hover:bg-blue-50 transition"
           data-testid="admin-dashboard-total-students">
            <h3 class="text-blue-700 text-xs lg:text-sm font-semibold">Total Students</h3>
            <p class="text-xl lg:text-3xl font-bold text-gray-900">{{ $stats['total_students'] }}</p>
            <p class="text-[11px] text-gray-400 mt-1">All-time</p>
        </a>

        <a href="{{ route('admin.courses.index') }}"
           class="rounded-lg border border-indigo-200 bg-indigo-50/60 p-4 lg:p-6 block hover:shadow-md hover:bg-indigo-50 transition"
           data-testid="admin-dashboard-total-courses">
            <h3 class="text-indigo-700 text-xs lg:text-sm font-semibold">Total Courses</h3>
            <p class="text-xl lg:text-3xl font-bold text-gray-900">{{ $stats['total_courses'] }}</p>
            <p class="text-[11px] text-gray-400 mt-1">All-time</p>
        </a>

        <a href="{{ route('admin.payments.index', ['from' => $analytics['range']['from'] ?? null, 'to' => $analytics['range']['to'] ?? null]) }}"
           class="rounded-lg border border-emerald-200 bg-emerald-50/60 p-4 lg:p-6 block hover:shadow-md hover:bg-emerald-50 transition"
           data-testid="admin-dashboard-total-revenue">
            <h3 class="text-emerald-700 text-xs lg:text-sm font-semibold">Total Revenue</h3>
            <p class="text-xl lg:text-3xl font-bold text-gray-900">Rs. {{ number_format($stats['total_revenue'], 2) }}</p>
            <p class="text-[11px] text-gray-400 mt-1">
                {{ $analytics['range']['from_label'] ?? '' }} → {{ $analytics['range']['to_label'] ?? '' }}
            </p>
        </a>

        <a href="{{ route('admin.courses.index', ['status' => 'draft']) }}"
           class="rounded-lg border border-amber-200 bg-amber-50/70 p-4 lg:p-6 block hover:shadow-md hover:bg-amber-50 transition"
           data-testid="admin-dashboard-pending-courses">
            <h3 class="text-amber-700 text-xs lg:text-sm font-semibold">Pending Courses</h3>
            <p class="text-xl lg:text-3xl font-bold text-amber-700">{{ $stats['pending_courses'] }}</p>
            <p class="text-[11px] text-gray-400 mt-1">All-time</p>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6" data-testid="admin-dashboard-panels">
        <div class="bg-white rounded-lg shadow p-4 lg:p-6" data-testid="admin-dashboard-recent-payments">
            <div class="flex items-start justify-between gap-3 mb-4">
                <h2 class="text-lg lg:text-xl font-bold">Recent Payments</h2>
                <a href="{{ route('admin.payments.index') }}"
                   class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline whitespace-nowrap">
                    View All Payments →
                </a>
            </div>
            <div class="space-y-4">
                @forelse($recentPayments as $payment)
                <div class="border-b pb-2">
                    <p class="font-medium">{{ $payment->user->name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-600">{{ $payment->book->title ?? 'N/A' }}</p>
                    <p class="text-sm font-semibold">Rs. {{ number_format($payment->amount ?? 0, 2) }}</p>
                </div>
                @empty
                <p class="text-gray-500">No recent payments</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 lg:p-6" data-testid="admin-dashboard-pending-course-approvals">
            <div class="flex items-start justify-between gap-3 mb-4">
                <h2 class="text-lg lg:text-xl font-bold">Pending Course Approvals</h2>
                <a href="{{ route('admin.courses.index', ['status' => 'draft']) }}"
                   class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline whitespace-nowrap">
                    View All Pending Courses →
                </a>
            </div>
            <div class="space-y-4">
                @forelse($pendingCourses as $course)
                <div class="border-b pb-2">
                    <p class="font-medium">{{ $course->title }}</p>
                    <p class="text-sm text-gray-600">By: {{ $course->teacher->name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-600">Subject: {{ $course->subject->name ?? 'N/A' }}</p>
                    <a href="{{ route('admin.courses.show', $course->id) }}" class="text-blue-600 text-sm hover:underline">View Course</a>
                </div>
                @empty
                <p class="text-gray-500">No pending courses</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 lg:p-6" data-testid="admin-dashboard-pending-device-resets">
            <div class="flex items-start justify-between gap-3 mb-4">
                <h2 class="text-lg lg:text-xl font-bold">Pending Device Resets</h2>
                <a href="{{ route('admin.devices.index', ['status' => 'pending_reset']) }}"
                   class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline whitespace-nowrap">
                    View All Reset Requests →
                </a>
            </div>
            <div class="space-y-4">
                @forelse($pendingDeviceResets as $device)
                <div class="border-b pb-2">
                    <p class="font-medium">{{ $device->user->name }}</p>
                    <p class="text-sm text-gray-600">{{ $device->device_name ?? 'Unknown Device' }}</p>
                    <p class="text-xs text-gray-500">{{ Str::limit($device->reset_request_reason, 50) }}</p>
                    <a href="{{ route('admin.devices.index') }}?status=pending_reset" class="text-blue-600 text-sm hover:underline">View Request</a>
                </div>
                @empty
                <p class="text-gray-500">No pending reset requests</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8 mt-6" data-testid="admin-dashboard-analytics">
        <div class="bg-white rounded-lg shadow p-4 lg:p-6 lg:col-span-3" data-testid="admin-dashboard-chart-revenue">
            <div class="flex items-start justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-lg lg:text-xl font-bold">Revenue</h2>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $analytics['range']['from_label'] ?? '' }}
                        @if(!empty($analytics['range']['to_label']))
                            → {{ $analytics['range']['to_label'] }}
                        @endif
                        · Completed payments only
                    </p>
                </div>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-end gap-2 flex-wrap">
                    <div>
                        <label class="block text-[11px] text-gray-500 mb-1">From</label>
                        <input type="date" name="from"
                               value="{{ $analytics['range']['from'] ?? '' }}"
                               class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-500 mb-1">To</label>
                        <input type="date" name="to"
                               value="{{ $analytics['range']['to'] ?? '' }}"
                               class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                                class="inline-flex items-center px-3 py-1.5 rounded-md bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                            Apply
                        </button>
                        <a href="{{ route('admin.dashboard') }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
            <div class="relative w-full" style="height: 280px;">
                <canvas id="adminRevenueChart" aria-label="Revenue chart" role="img"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 lg:p-6" data-testid="admin-dashboard-chart-payments-status">
            <div class="mb-4">
                <h2 class="text-lg lg:text-xl font-bold">Payments by Status</h2>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $analytics['range']['from_label'] ?? '' }} → {{ $analytics['range']['to_label'] ?? '' }}
                </p>
            </div>
            <div class="relative w-full" style="height: 280px;">
                <canvas id="adminPaymentStatusChart" aria-label="Payments by status chart" role="img"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 lg:p-6" data-testid="admin-dashboard-chart-students-activity">
            <div class="mb-4">
                <h2 class="text-lg lg:text-xl font-bold">Students Activity</h2>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $analytics['range']['from_label'] ?? '' }} → {{ $analytics['range']['to_label'] ?? '' }}
                </p>
            </div>
            <div class="relative w-full" style="height: 280px;">
                <canvas id="adminStudentsActivityChart" aria-label="Students activity chart" role="img"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 lg:p-6" data-testid="admin-dashboard-chart-device-bindings">
            <div class="mb-4">
                <h2 class="text-lg lg:text-xl font-bold">Student Devices (Device Binding)</h2>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $analytics['range']['from_label'] ?? '' }} → {{ $analytics['range']['to_label'] ?? '' }}
                </p>
            </div>
            <div class="relative w-full" style="height: 280px;">
                <canvas id="adminDeviceBindingsChart" aria-label="Device bindings chart" role="img"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 lg:p-6 lg:col-span-3" data-testid="admin-dashboard-chart-courses-status">
            <div class="mb-4">
                <h2 class="text-lg lg:text-xl font-bold">Courses by Status</h2>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $analytics['range']['from_label'] ?? '' }} → {{ $analytics['range']['to_label'] ?? '' }}
                </p>
            </div>
            <div class="relative w-full" style="height: 260px;">
                <canvas id="adminCourseStatusChart" aria-label="Courses by status chart" role="img"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.Chart) return;

            const analytics = @json($analytics ?? []);

            const titleCase = (value) => String(value || '')
                .replace(/[_-]+/g, ' ')
                .replace(/\b\w/g, (c) => c.toUpperCase());

            // Revenue (line)
            const revenueCanvas = document.getElementById('adminRevenueChart');
            if (revenueCanvas && analytics?.revenue_last_30_days) {
                const ctx = revenueCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: analytics.revenue_last_30_days.labels || [],
                        datasets: [{
                            label: 'Revenue (Rs.)',
                            data: analytics.revenue_last_30_days.data || [],
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.12)',
                            tension: 0.35,
                            fill: true,
                            pointRadius: 0,
                            borderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => `Rs. ${Number(ctx.parsed.y || 0).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`,
                                },
                            },
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: { ticks: { callback: (v) => `Rs. ${Number(v).toLocaleString()}` } },
                        },
                    },
                });
            }

            // Payments by status (doughnut)
            const payCanvas = document.getElementById('adminPaymentStatusChart');
            if (payCanvas && analytics?.payments_by_status) {
                const ctx = payCanvas.getContext('2d');
                const rawLabelsValue = analytics.payments_by_status.labels;
                const rawDataValue = analytics.payments_by_status.data;

                const rawLabels = Array.isArray(rawLabelsValue)
                    ? rawLabelsValue
                    : Object.values(rawLabelsValue || {});

                const rawData = Array.isArray(rawDataValue)
                    ? rawDataValue
                    : Object.values(rawDataValue || {});

                const labels = rawLabels.map(titleCase);
                const data = rawData.map((v) => Number(v || 0));

                const bg = rawLabels.map((s) => {
                    const key = String(s || '').toLowerCase();
                    if (key === 'completed') return '#16a34a';
                    if (key === 'pending') return '#f59e0b';
                    if (key === 'failed') return '#ef4444';
                    if (key === 'cancelled') return '#6b7280';
                    return '#0ea5e9';
                });

                const total = data.reduce((sum, v) => sum + v, 0);
                const finalLabels = total > 0 ? labels : ['No data (selected range)'];
                const finalData = total > 0 ? data : [1];
                const finalBg = total > 0 ? bg : ['#e5e7eb'];

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: finalLabels,
                        datasets: [{
                            data: finalData,
                            backgroundColor: finalBg,
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                        },
                        cutout: '65%',
                    },
                });
            }

            // Courses by status (bar)
            const courseCanvas = document.getElementById('adminCourseStatusChart');
            if (courseCanvas && analytics?.courses_by_status) {
                const ctx = courseCanvas.getContext('2d');
                const labels = (analytics.courses_by_status.labels || []).map(titleCase);
                const data = analytics.courses_by_status.data || [];

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Courses',
                            data,
                            backgroundColor: 'rgba(2, 132, 199, 0.18)',
                            borderColor: '#0284c7',
                            borderWidth: 1,
                            borderRadius: 8,
                            maxBarThickness: 44,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: { beginAtZero: true, ticks: { precision: 0 } },
                        },
                    },
                });
            }

            // Students activity (doughnut)
            const studentsCanvas = document.getElementById('adminStudentsActivityChart');
            if (studentsCanvas && analytics?.students_activity) {
                const ctx = studentsCanvas.getContext('2d');
                const labels = analytics.students_activity.labels || ['Active', 'Offline'];
                const data = analytics.students_activity.data || [0, 0];

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            data,
                            backgroundColor: ['#16a34a', '#9ca3af'],
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                        },
                        cutout: '65%',
                    },
                });
            }

            // Device bindings by status (doughnut)
            const devicesCanvas = document.getElementById('adminDeviceBindingsChart');
            if (devicesCanvas && analytics?.student_devices_by_status) {
                const ctx = devicesCanvas.getContext('2d');
                const rawLabelsValue = analytics.student_devices_by_status.labels;
                const rawDataValue = analytics.student_devices_by_status.data;

                const rawLabels = Array.isArray(rawLabelsValue)
                    ? rawLabelsValue
                    : Object.values(rawLabelsValue || {});

                const rawData = Array.isArray(rawDataValue)
                    ? rawDataValue
                    : Object.values(rawDataValue || {});

                const labels = rawLabels.map(titleCase);
                const data = rawData.map((v) => Number(v || 0));

                // Match colors to known statuses in the same order as provided by backend.
                const bg = rawLabels.map((s) => {
                    const key = String(s || '').toLowerCase();
                    if (key === 'active') return '#16a34a';
                    if (key === 'pending_reset') return '#f59e0b';
                    if (key === 'blocked') return '#ef4444';
                    return '#6b7280';
                });

                const total = data.reduce((sum, v) => sum + v, 0);
                const finalLabels = total > 0 ? labels : ['No data (selected range)'];
                const finalData = total > 0 ? data : [1];
                const finalBg = total > 0 ? bg : ['#e5e7eb'];

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: finalLabels,
                        datasets: [{
                            data: finalData,
                            backgroundColor: finalBg,
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                        },
                        cutout: '65%',
                    },
                });
            }
        });
    </script>
@endpush

