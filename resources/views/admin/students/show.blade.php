@extends('layouts.admin')

@section('title', 'Student Details')
@section('page-title', 'Student Details')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Student Info -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
        <div class="flex items-center space-x-4 mb-6">
            <x-user-avatar :user="$student" size="xl" class="!h-20 !w-20 !text-xl border-4 border-blue-100" />
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $student->name }}</h2>
                <p class="text-gray-600">{{ $student->email }}</p>
                <p class="text-gray-600">{{ $student->mobile ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <span class="text-sm text-gray-500">Status</span>
                <p class="font-semibold">
                    <span class="px-2 py-1 text-xs rounded-full {{ $student->status == 'active' ? 'bg-green-100 text-green-800' : ($student->status == 'suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($student->status) }}
                    </span>
                </p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Joined</span>
                <p class="font-semibold">{{ $student->created_at->format('M d, Y') }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Last Login</span>
                <p class="font-semibold">{{ $student->last_login_at ? $student->last_login_at->format('M d, Y H:i') : 'Never' }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Email Verified</span>
                <p class="font-semibold">
                    <span class="px-2 py-1 text-xs rounded-full {{ $student->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $student->email_verified_at ? 'Verified' : 'Not Verified' }}
                    </span>
                </p>
            </div>
        </div>

        <div class="flex gap-2 mb-6">
            <a href="{{ route('admin.students.edit', $student->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Edit Student
            </a>
            <a href="{{ route('admin.students.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                Back to Students
            </a>
        </div>

        <!-- Student Statistics -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $studentStats['total_enrollments'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Enrollments</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $studentStats['completed_courses'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Completed Courses</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-purple-600">{{ number_format($studentStats['total_payments'] ?? 0, 0) }}</div>
                <div class="text-sm text-gray-600">Total Paid (Rs.)</div>
            </div>
            <div class="bg-orange-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-orange-600">{{ $studentStats['total_lessons_completed'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Lessons Completed</div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Enrollments -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Course Enrollments ({{ $student->enrollments->count() }})
            </h3>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($student->enrollments as $enrollment)
                    <div class="border-b pb-3 last:border-0">
                        <p class="font-medium text-gray-900">{{ $enrollment->book->title }}</p>
                        <p class="text-sm text-gray-600 mt-1">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $enrollment->status == 'completed' ? 'bg-green-100 text-green-800' : ($enrollment->status == 'active' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">{{ $enrollment->created_at->format('M d, Y') }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No enrollments yet</p>
                @endforelse
            </div>
        </div>

        <!-- Payments -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Recent Payments ({{ $student->payments->count() }})
            </h3>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @forelse($student->payments->take(10) as $payment)
                    <div class="border-b pb-3 last:border-0">
                        <p class="font-medium text-green-600">Rs. {{ number_format($payment->amount, 2) }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $payment->book->title ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $payment->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                            - {{ $payment->created_at->format('M d, Y') }}
                        </p>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No payments yet</p>
                @endforelse
            </div>
        </div>

        <!-- Devices -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                </svg>
                Device Bindings ({{ $studentStats['devices_count'] ?? 0 }})
            </h3>
            <div class="space-y-3 max-h-48 overflow-y-auto">
                @forelse($student->deviceBindings as $device)
                    <div class="border-b pb-3 last:border-0">
                        <p class="text-sm font-medium text-gray-900">{{ $device->device_name ?? 'Unknown Device' }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $device->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($device->status) }}
                            </span>
                            - {{ $device->last_used_at ? $device->last_used_at->format('M d, Y') : 'Never used' }}
                        </p>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No devices bound</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
