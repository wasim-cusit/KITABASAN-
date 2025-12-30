@extends('layouts.admin')

@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')

@section('content')
<div class="space-y-6">
    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex gap-4">
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="px-4 py-2 border rounded-lg">
            <input type="date" name="date_to" value="{{ $dateTo }}" class="px-4 py-2 border rounded-lg">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Apply Filter
            </button>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm text-gray-500 mb-2">Total Users</h3>
            <p class="text-3xl font-bold">{{ $userStats['total'] }}</p>
            <div class="mt-2 text-sm">
                <span class="text-gray-600">Teachers: {{ $userStats['teachers'] }}</span><br>
                <span class="text-gray-600">Students: {{ $userStats['students'] }}</span>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm text-gray-500 mb-2">Total Courses</h3>
            <p class="text-3xl font-bold">{{ $courseStats['total'] }}</p>
            <div class="mt-2 text-sm">
                <span class="text-green-600">Published: {{ $courseStats['published'] }}</span><br>
                <span class="text-yellow-600">Draft: {{ $courseStats['draft'] }}</span>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm text-gray-500 mb-2">Total Revenue</h3>
            <p class="text-3xl font-bold text-green-600">Rs. {{ number_format($paymentStats['completed'], 2) }}</p>
            <div class="mt-2 text-sm">
                <span class="text-gray-600">Period: {{ $dateFrom }} to {{ $dateTo }}</span>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm text-gray-500 mb-2">Payments</h3>
            <p class="text-3xl font-bold">{{ $paymentStats['total'] }}</p>
            <div class="mt-2 text-sm">
                <span class="text-yellow-600">Pending: {{ $paymentStats['pending'] }}</span><br>
                <span class="text-red-600">Failed: {{ $paymentStats['failed'] }}</span>
            </div>
        </div>
    </div>

    <!-- Top Courses -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4">Top Courses by Enrollment</h3>
            <div class="space-y-3">
                @forelse($topCourses as $course)
                    <div class="flex items-center justify-between border-b pb-2">
                        <div>
                            <p class="font-medium">{{ $course->title }}</p>
                            <p class="text-sm text-gray-600">{{ $course->enrollments_count }} enrollments</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No data available</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4">Top Courses by Revenue</h3>
            <div class="space-y-3">
                @forelse($topRevenueCourses as $course)
                    <div class="flex items-center justify-between border-b pb-2">
                        <div>
                            <p class="font-medium">{{ $course->title }}</p>
                            <p class="text-sm text-gray-600">Rs. {{ number_format($course->total_revenue ?? 0, 2) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No data available</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

