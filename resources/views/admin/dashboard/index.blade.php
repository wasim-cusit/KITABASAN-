    @extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="container mx-auto px-0 lg:px-4">

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-6 lg:mb-8">
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h3 class="text-gray-500 text-xs lg:text-sm font-medium">Total Users</h3>
            <p class="text-xl lg:text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h3 class="text-gray-500 text-xs lg:text-sm font-medium">Total Courses</h3>
            <p class="text-xl lg:text-3xl font-bold text-gray-900">{{ $stats['total_courses'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h3 class="text-gray-500 text-xs lg:text-sm font-medium">Total Revenue</h3>
            <p class="text-xl lg:text-3xl font-bold text-gray-900">Rs. {{ number_format($stats['total_revenue'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h3 class="text-gray-500 text-xs lg:text-sm font-medium">Pending Courses</h3>
            <p class="text-xl lg:text-3xl font-bold text-yellow-600">{{ $stats['pending_courses'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h2 class="text-lg lg:text-xl font-bold mb-4">Recent Payments</h2>
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

        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h2 class="text-lg lg:text-xl font-bold mb-4">Pending Course Approvals</h2>
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

        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h2 class="text-lg lg:text-xl font-bold mb-4">Pending Device Resets</h2>
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
            @if(isset($pendingDeviceResets) && $pendingDeviceResets->count() > 0)
                <a href="{{ route('admin.devices.index') }}?status=pending_reset" class="mt-4 inline-block text-blue-600 text-sm hover:underline font-semibold">
                    View All Requests →
                </a>
            @endif
        </div>
    </div>
</div>
@endsection

