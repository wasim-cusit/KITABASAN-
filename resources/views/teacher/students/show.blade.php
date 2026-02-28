@extends('layouts.teacher')

@section('title', 'Student Details')
@section('page-title', 'Student Details')

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    <div class="mb-4">
        <a href="{{ route('teacher.students.index') }}"
           class="inline-flex items-center px-3 py-1.5 rounded-md border border-blue-200 bg-blue-50 text-xs lg:text-sm font-medium text-blue-700 hover:bg-blue-100 hover:border-blue-300 no-underline transition">
            ← Back to Students
        </a>
    </div>

    <!-- Student Info Card -->
    <div class="bg-white rounded-lg shadow p-3 lg:p-6 mb-4 lg:mb-6">
        <div class="flex flex-row items-center gap-3 sm:gap-4">
            <div class="flex-shrink-0">
                <x-user-avatar :user="$student" size="custom" class="h-12 w-12 sm:h-16 sm:w-16 lg:h-20 lg:w-20 text-sm sm:text-base lg:text-lg" />
            </div>
            <div class="flex-1">
                @php
                    $isOnline = $student->last_login_at && $student->last_login_at->gt(now()->subMinutes(5));
                @endphp
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">{{ $student->name }}</h2>
                    @if($isOnline)
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500" title="Online"></span>
                    @else
                        <span class="w-2.5 h-2.5 rounded-full bg-gray-400" title="Offline"></span>
                    @endif
                </div>
                <p class="text-xs sm:text-sm lg:text-base text-gray-600 mt-1 break-all sm:break-normal">{{ $student->email }}</p>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-2.5 lg:gap-5 mb-4 lg:mb-6">
        <div class="bg-blue-50 border border-blue-100 rounded-lg shadow-sm p-2.5 lg:p-4 text-center">
            <h3 class="text-[10px] sm:text-xs lg:text-sm font-medium text-slate-600 text-left">
                Total courses
            </h3>
            <p class="mt-0.5 text-base sm:text-xl lg:text-2xl font-bold text-slate-900">
                {{ $stats['total_courses'] }}
            </p>
        </div>
        <div class="bg-green-50 border border-green-100 rounded-lg shadow-sm p-2.5 lg:p-4 text-center">
            <h3 class="text-[10px] sm:text-xs lg:text-sm font-medium text-slate-600 text-left">
                Active enrollments
            </h3>
            <p class="mt-0.5 text-base sm:text-xl lg:text-2xl font-bold text-green-700">
                {{ $stats['active_enrollments'] }}
            </p>
        </div>
        <div class="bg-indigo-50 border border-indigo-100 rounded-lg shadow-sm p-2.5 lg:p-4 text-center">
            <h3 class="text-[10px] sm:text-xs lg:text-sm font-medium text-slate-600 text-left">
                Average progress
            </h3>
            <p class="mt-0.5 text-base sm:text-xl lg:text-2xl font-bold text-indigo-700">
                {{ number_format($stats['average_progress'], 1) }}%
            </p>
        </div>
    </div>

    <!-- Enrollments -->
    <div class="bg-white rounded-lg shadow p-4 lg:p-6">
        <h2 class="text-lg lg:text-xl font-bold mb-4">Course Enrollments</h2>

        @if($enrollments->count() > 0)
            <div class="overflow-x-auto -mx-4 lg:mx-0">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 capitalize">Course</th>
                                    <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 capitalize hidden sm:table-cell">Subject</th>
                                    <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 capitalize">Status</th>
                                    <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 capitalize">Progress</th>
                                    <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 capitalize hidden md:table-cell">Enrolled</th>
                                    <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 capitalize hidden md:table-cell">Ended</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($enrollments as $enrollment)
                                    <tr>
                                        <td class="px-3 lg:px-6 py-4">
                                            <div class="text-xs lg:text-sm font-medium text-gray-900">{{ $enrollment->book->title }}</div>
                                            <div class="text-xs text-gray-500 sm:hidden mt-1">{{ $enrollment->book->subject->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-xs lg:text-sm text-gray-500 hidden sm:table-cell">
                                            {{ $enrollment->book->subject->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-3 lg:px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $enrollment->status == 'active' ? 'bg-green-100 text-green-800' :
                                                   ($enrollment->status == 'expired' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-3 lg:px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                                </div>
                                                <span class="text-xs lg:text-sm text-gray-700">{{ $enrollment->progress_percentage }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-xs lg:text-sm text-gray-500 hidden md:table-cell">
                                            {{ $enrollment->enrolled_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-xs lg:text-sm text-gray-500 hidden md:table-cell">
                                            {{ $enrollment->expires_at ? $enrollment->expires_at->format('M d, Y') : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <p class="text-gray-500 text-sm lg:text-base text-center py-8">No enrollments found</p>
        @endif
    </div>
</div>
@endsection

