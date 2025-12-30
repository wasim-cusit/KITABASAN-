@extends('layouts.student')

@section('title', 'Student Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-6 mb-6 lg:mb-8">
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h3 class="text-gray-500 text-xs lg:text-sm font-medium">Enrolled Courses</h3>
            <p class="text-xl lg:text-3xl font-bold text-gray-900">{{ $stats['enrolled_courses'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h3 class="text-gray-500 text-xs lg:text-sm font-medium">Completed Courses</h3>
            <p class="text-xl lg:text-3xl font-bold text-green-600">{{ $stats['completed_courses'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h3 class="text-gray-500 text-xs lg:text-sm font-medium">In Progress</h3>
            <p class="text-xl lg:text-3xl font-bold text-blue-600">{{ $stats['in_progress_courses'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h2 class="text-lg lg:text-xl font-bold mb-4">My Courses</h2>
            <div class="space-y-4">
                @forelse($enrollments as $enrollment)
                <div class="border-b pb-4 last:border-b-0">
                    <h3 class="font-medium text-sm lg:text-base">{{ $enrollment->book->title }}</h3>
                    <p class="text-xs lg:text-sm text-gray-600 mt-1">{{ $enrollment->book->subject->name }}</p>
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $enrollment->progress_percentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $enrollment->progress_percentage }}% Complete</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-sm lg:text-base">No enrolled courses yet. Browse courses to get started!</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h2 class="text-lg lg:text-xl font-bold mb-4">Recent Activity</h2>
            <div class="space-y-4">
                @forelse($recentProgress as $progress)
                <div class="border-b pb-2 last:border-b-0">
                    <p class="font-medium text-xs lg:text-sm">{{ $progress->lesson->title }}</p>
                    <p class="text-xs text-gray-600 mt-1">{{ $progress->lesson->chapter->book->title }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $progress->last_watched_at->diffForHumans() }}</p>
                </div>
                @empty
                <p class="text-gray-500 text-sm lg:text-base">No recent activity</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

