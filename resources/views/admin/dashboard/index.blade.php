    @extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6">

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8" data-testid="admin-dashboard-stats">
        <a href="{{ route('admin.students.index') }}"
           class="bg-white border border-gray-200 rounded-lg p-5 block hover:border-blue-200 hover:shadow-sm transition-all duration-150 border-l-4 border-l-blue-500"
           data-testid="admin-dashboard-total-students">
            <p class="text-sm font-medium text-blue-600">Total Students</p>
            <p class="mt-1 text-xl font-semibold text-gray-900 tabular-nums">{{ $stats['total_students'] }}</p>
        </a>
        <a href="{{ route('admin.courses.index') }}"
           class="bg-white border border-gray-200 rounded-lg p-5 block hover:border-indigo-200 hover:shadow-sm transition-all duration-150 border-l-4 border-l-indigo-500"
           data-testid="admin-dashboard-total-courses">
            <p class="text-sm font-medium text-indigo-600">Total Courses</p>
            <p class="mt-1 text-xl font-semibold text-gray-900 tabular-nums">{{ $stats['total_courses'] }}</p>
        </a>
        <a href="{{ route('admin.payments.index', ['from' => $analytics['range']['from'] ?? null, 'to' => $analytics['range']['to'] ?? null]) }}"
           class="bg-white border border-gray-200 rounded-lg p-5 block hover:border-emerald-200 hover:shadow-sm transition-all duration-150 border-l-4 border-l-emerald-500"
           data-testid="admin-dashboard-total-revenue">
            <p class="text-sm font-medium text-emerald-600">Revenue (selected range)</p>
            <p class="mt-1 text-xl font-semibold text-emerald-700 tabular-nums truncate" title="Rs. {{ number_format($stats['total_revenue'], 0) }}">Rs. {{ number_format($stats['total_revenue'], 0) }}</p>
        </a>
        <a href="{{ route('admin.courses.index', ['status' => 'draft']) }}"
           class="bg-white border border-gray-200 rounded-lg p-5 block hover:border-amber-200 hover:shadow-sm transition-all duration-150 border-l-4 border-l-amber-500"
           data-testid="admin-dashboard-pending-courses">
            <p class="text-sm font-medium text-amber-600">Pending course approvals</p>
            <p class="mt-1 text-xl font-semibold text-gray-900 tabular-nums">{{ $stats['pending_courses'] }}</p>
        </a>
    </div>

    <!-- Activity panels -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8" data-testid="admin-dashboard-panels">
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden" data-testid="admin-dashboard-recent-payments">
            <div class="px-5 py-4 border-b border-emerald-100 bg-emerald-50/70">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900">Recent payments</h2>
                    <a href="{{ route('admin.payments.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">View all</a>
                </div>
            </div>
            <div class="p-5">
                <ul class="divide-y divide-gray-200">
                    @forelse($recentPayments as $payment)
                    <li class="py-3 first:pt-0 last:pb-0">
                        <div class="flex justify-between items-baseline gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $payment->user->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $payment->book->title ?? 'N/A' }}</p>
                            </div>
                            <span class="text-sm font-medium text-emerald-700 tabular-nums shrink-0">Rs. {{ number_format($payment->amount ?? 0, 0) }}</span>
                        </div>
                    </li>
                    @empty
                    <li class="py-8 text-center">
                        <p class="text-sm text-gray-500">No recent payments</p>
                        <a href="{{ route('admin.payments.index') }}" class="mt-2 text-sm font-medium text-blue-600 hover:text-blue-700">View payments</a>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden" data-testid="admin-dashboard-pending-course-approvals">
            <div class="px-5 py-4 border-b border-amber-100 bg-amber-50/70">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900">Pending course approvals</h2>
                    <a href="{{ route('admin.courses.index', ['status' => 'draft']) }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">View all</a>
                </div>
            </div>
            <div class="p-5">
                <ul class="divide-y divide-gray-200">
                    @forelse($pendingCourses as $course)
                    <li class="py-3 first:pt-0 last:pb-0">
                        <p class="text-sm font-medium text-gray-900 line-clamp-2">{{ $course->title }}</p>
                        <p class="text-xs text-gray-500 mt-1">Teacher: {{ $course->teacher->name ?? 'N/A' }} · {{ $course->subject->name ?? 'N/A' }}</p>
                        <a href="{{ route('admin.courses.show', $course->id) }}" class="mt-2 inline-block text-sm font-medium text-blue-600 hover:text-blue-700">Review</a>
                    </li>
                    @empty
                    <li class="py-8 text-center">
                        <p class="text-sm text-gray-500">No pending courses</p>
                        <a href="{{ route('admin.courses.index', ['status' => 'draft']) }}" class="mt-2 text-sm font-medium text-blue-600 hover:text-blue-700">View courses</a>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden" data-testid="admin-dashboard-pending-device-resets">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/70">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900">Pending device resets</h2>
                    <a href="{{ route('admin.devices.index', ['status' => 'pending_reset']) }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">View all</a>
                </div>
            </div>
            <div class="p-5">
                <ul class="divide-y divide-gray-200">
                    @forelse($pendingDeviceResets as $device)
                    <li class="py-3 first:pt-0 last:pb-0">
                        <p class="text-sm font-medium text-gray-900">{{ $device->user->name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Device: {{ $device->device_name ?? 'Unknown' }}</p>
                        @if($device->reset_request_reason)
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2" title="{{ $device->reset_request_reason }}">{{ Str::limit($device->reset_request_reason, 60) }}</p>
                        @endif
                        <a href="{{ route('admin.devices.index') }}?status=pending_reset" class="mt-2 inline-block text-sm font-medium text-blue-600 hover:text-blue-700">Manage</a>
                    </li>
                    @empty
                    <li class="py-8 text-center">
                        <p class="text-sm text-gray-500">No pending reset requests</p>
                        <a href="{{ route('admin.devices.index', ['status' => 'pending_reset']) }}" class="mt-2 text-sm font-medium text-blue-600 hover:text-blue-700">View requests</a>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 mt-8" data-testid="admin-dashboard-analytics">
        <div class="bg-white border border-gray-200 rounded-lg p-5 lg:col-span-3" data-testid="admin-dashboard-chart-revenue">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-sm font-semibold text-gray-900">Revenue</h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        @if(!empty($analytics['range']['all_time']))
                            All time · Completed payments only
                        @else
                            {{ $analytics['range']['from_label'] ?? '' }}
                            @if(!empty($analytics['range']['to_label']))
                                – {{ $analytics['range']['to_label'] }}
                            @endif
                            · Completed payments only
                        @endif
                    </p>
                </div>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">From</label>
                        <input type="date" name="from" value="{{ !empty($analytics['range']['all_time']) ? '' : ($analytics['range']['from'] ?? '') }}"
                               class="block w-full rounded border border-gray-300 px-2.5 py-1.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">To</label>
                        <input type="date" name="to" value="{{ !empty($analytics['range']['all_time']) ? '' : ($analytics['range']['to'] ?? '') }}"
                               class="block w-full rounded border border-gray-300 px-2.5 py-1.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="inline-flex items-center rounded bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">Apply</button>
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center rounded border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset</a>
                    </div>
                </form>
            </div>
            <div class="relative w-full" style="height: 280px;">
                <canvas id="adminRevenueChart" aria-label="Revenue chart" role="img"></canvas>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-5" data-testid="admin-dashboard-chart-payments-status">
            <h2 class="text-sm font-semibold text-gray-900">Payments by status</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $analytics['range']['from_label'] ?? '' }} – {{ $analytics['range']['to_label'] ?? '' }}</p>
            <div class="relative mt-4 w-full" style="height: 280px;">
                <canvas id="adminPaymentStatusChart" aria-label="Payments by status chart" role="img"></canvas>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-5" data-testid="admin-dashboard-chart-students-activity">
            <h2 class="text-sm font-semibold text-gray-900">Students activity</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $analytics['range']['from_label'] ?? '' }} – {{ $analytics['range']['to_label'] ?? '' }}</p>
            <div class="relative mt-4 w-full" style="height: 280px;">
                <canvas id="adminStudentsActivityChart" aria-label="Students activity chart" role="img"></canvas>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-5" data-testid="admin-dashboard-chart-device-bindings">
            <h2 class="text-sm font-semibold text-gray-900">Student devices</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $analytics['range']['from_label'] ?? '' }} – {{ $analytics['range']['to_label'] ?? '' }}</p>
            <div class="relative mt-4 w-full" style="height: 280px;">
                <canvas id="adminDeviceBindingsChart" aria-label="Device bindings chart" role="img"></canvas>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-5 lg:col-span-3" data-testid="admin-dashboard-chart-courses-status">
            <h2 class="text-sm font-semibold text-gray-900">Courses by status</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $analytics['range']['from_label'] ?? '' }} – {{ $analytics['range']['to_label'] ?? '' }}</p>
            <div class="relative mt-4 w-full" style="height: 260px;">
                <canvas id="adminCourseStatusChart" aria-label="Courses by status chart" role="img"></canvas>
            </div>
        </div>

        <!-- Student login locations -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden lg:col-span-3 mt-6" data-testid="admin-dashboard-student-map">
            <div class="px-5 py-4 border-b border-blue-100 bg-blue-50/70">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Student login locations</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Approximate location by device IP at sign-in</p>
                    </div>
                    <a href="{{ route('admin.students.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">View students</a>
                </div>
            </div>
            <div class="p-5">
                <div class="rounded-md border border-gray-200 overflow-hidden bg-gray-50" style="min-height: 360px;">
                    <div id="adminStudentMap" class="w-full" style="height: 360px;"></div>
                </div>
                @if(empty($studentLoginLocations ?? []))
                <div class="mt-4 rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                    <p class="text-sm text-gray-600">No location data yet. Data appears when students log in from a non-local IP.</p>
                </div>
                @else
                <p class="mt-3 text-xs text-gray-500">OpenStreetMap. Locations are approximate.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
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

            // Student login locations map (Leaflet)
            const mapEl = document.getElementById('adminStudentMap');
            const locations = @json($studentLoginLocations ?? []);
            if (mapEl && typeof L !== 'undefined') {
                const map = L.map('adminStudentMap').setView([20, 0], 2);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);
                if (locations.length > 0) {
                    const bounds = [];
                    locations.forEach(function (loc) {
                        const m = L.marker([loc.lat, loc.lng]).addTo(map);
                        const label = [loc.name, loc.city, loc.country].filter(Boolean).join(', ') || loc.ip;
                        m.bindPopup('<strong>' + (loc.name || 'Student') + '</strong><br>' + (loc.city ? loc.city + ', ' : '') + (loc.country || '') + (loc.ip ? '<br><small>IP: ' + loc.ip + '</small>' : ''));
                        bounds.push([loc.lat, loc.lng]);
                    });
                    if (bounds.length > 1) {
                        map.fitBounds(bounds, { padding: [30, 30] });
                    } else if (bounds.length === 1) {
                        map.setView(bounds[0], 8);
                    }
                }
            }
        });
    </script>
@endpush

