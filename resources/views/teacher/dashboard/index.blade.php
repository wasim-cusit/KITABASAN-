@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-6 lg:mb-8">
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h3 class="text-gray-500 text-xs lg:text-sm font-medium">Total Courses</h3>
            <p class="text-xl lg:text-3xl font-bold text-gray-900">{{ $stats['total_courses'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h3 class="text-gray-500 text-xs lg:text-sm font-medium">Total Students</h3>
            <p class="text-xl lg:text-3xl font-bold text-gray-900">{{ $stats['total_students'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h3 class="text-gray-500 text-xs lg:text-sm font-medium">Total Enrollments</h3>
            <p class="text-xl lg:text-3xl font-bold text-gray-900">{{ $stats['total_enrollments'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h3 class="text-gray-500 text-xs lg:text-sm font-medium">Pending Courses</h3>
            <p class="text-xl lg:text-3xl font-bold text-yellow-600">{{ $stats['pending_courses'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4 lg:p-6">
        <h2 class="text-lg lg:text-xl font-bold mb-4">My Courses</h2>
        <div class="space-y-4">
            @forelse($myCourses as $course)
            <div class="border-b pb-4 last:border-b-0">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="flex-1">
                        <h3 class="font-medium text-base lg:text-lg">{{ $course->title }}</h3>
                        <p class="text-xs lg:text-sm text-gray-600 mt-1">{{ $course->subject->name }}</p>
                        <p class="text-xs lg:text-sm mt-1">Enrollments: {{ $course->enrollments->count() }}</p>
                    </div>
                    <span class="inline-block px-2 py-1 text-xs rounded w-fit {{ $course->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($course->status) }}
                    </span>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-sm lg:text-base">No courses yet. Create your first course!</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

