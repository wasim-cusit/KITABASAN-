@extends('layouts.teacher')

@section('title', 'Student Details')
@section('page-title', 'Student Details')

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    <div class="mb-4">
        <a href="{{ route('teacher.students.index') }}" class="text-blue-600 hover:text-blue-900 text-sm lg:text-base">
            ← Back to Students
        </a>
    </div>

    <!-- Student Info Card -->
    <div class="bg-white rounded-lg shadow p-4 lg:p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="flex-shrink-0">
                <img class="h-16 w-16 lg:h-20 lg:w-20 rounded-full" src="{{ $student->profile_image ? Storage::url($student->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) }}" alt="">
            </div>
            <div class="flex-1">
                <h2 class="text-xl lg:text-2xl font-bold text-gray-900">{{ $student->name }}</h2>
                <p class="text-sm lg:text-base text-gray-600 mt-1">{{ $student->email }}</p>
                @if($student->mobile)
                    <p class="text-sm lg:text-base text-gray-600">{{ $student->mobile }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h3 class="text-gray-500 text-xs lg:text-sm font-medium">Total Courses</h3>
            <p class="text-xl lg:text-3xl font-bold text-gray-900">{{ $stats['total_courses'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h3 class="text-gray-500 text-xs lg:text-sm font-medium">Active Enrollments</h3>
            <p class="text-xl lg:text-3xl font-bold text-green-600">{{ $stats['active_enrollments'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h3 class="text-gray-500 text-xs lg:text-sm font-medium">Average Progress</h3>
            <p class="text-xl lg:text-3xl font-bold text-blue-600">{{ number_format($stats['average_progress'], 1) }}%</p>
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
                                    <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                                    <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Subject</th>
                                    <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                                    <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Enrolled</th>
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

