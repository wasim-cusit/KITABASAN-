@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Teacher Dashboard</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium">Total Courses</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_courses'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium">Total Students</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_students'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium">Total Enrollments</h3>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_enrollments'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium">Pending Courses</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_courses'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">My Courses</h2>
        <div class="space-y-4">
            @forelse($myCourses as $course)
            <div class="border-b pb-4">
                <h3 class="font-medium text-lg">{{ $course->title }}</h3>
                <p class="text-sm text-gray-600">{{ $course->subject->name }}</p>
                <p class="text-sm">Enrollments: {{ $course->enrollments->count() }}</p>
                <span class="inline-block px-2 py-1 text-xs rounded {{ $course->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst($course->status) }}
                </span>
            </div>
            @empty
            <p class="text-gray-500">No courses yet. Create your first course!</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

