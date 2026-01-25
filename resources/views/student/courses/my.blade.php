@extends('layouts.student')

@section('title', 'My Courses')
@section('page-title', 'My Courses')

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    <div class="bg-white rounded-lg shadow p-4 lg:p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl lg:text-2xl font-bold">My Courses</h1>
            <a href="{{ route('student.courses.index') }}"
               class="text-sm text-blue-600 hover:text-blue-700">
                Browse Courses
            </a>
        </div>

        @if($enrollments->isEmpty())
            <div class="text-center py-10">
                <p class="text-gray-600 mb-4">You have not enrolled in any courses yet.</p>
                <a href="{{ route('student.courses.index') }}"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                    Browse Courses
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($enrollments as $enrollment)
                    @php
                        $course = $enrollment->book;
                        $chapters = $course->chapters ?? collect();
                        $totalLessons = $chapters->sum(fn($ch) => $ch->lessons->count());
                        $completedLessons = $enrollment->lessonProgress
                            ? $enrollment->lessonProgress->where('is_completed', true)->count()
                            : 0;
                        $progressPercentage = $totalLessons > 0
                            ? round(($completedLessons / $totalLessons) * 100)
                            : ($enrollment->progress_percentage ?? 0);
                    @endphp

                    <div class="border border-gray-200 rounded-xl p-4 flex flex-col gap-4 bg-white shadow-sm hover:shadow-md transition">
                        <div class="space-y-1">
                            <h3 class="text-base lg:text-lg font-semibold text-gray-900 line-clamp-2">{{ $course->title }}</h3>
                            <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-blue-100 text-blue-800">
                                {{ $course->subject->name ?? 'Course' }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2 text-xs text-gray-600 flex-wrap">
                            <span class="px-3 py-1.5 font-semibold {{ $course->is_free ? 'bg-green-100 text-green-800' : 'bg-amber-200 text-amber-900' }}">
                                {{ $course->is_free ? 'Free' : 'Paid' }}
                            </span>
                            @if($enrollment->expires_at)
                                <span class="text-gray-500">Access until {{ $enrollment->expires_at->format('M d, Y') }}</span>
                            @else
                                <span class="text-gray-500">Lifetime access</span>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span class="uppercase tracking-wide">Progress</span>
                                <span class="font-semibold text-blue-600">{{ $progressPercentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $completedLessons }} of {{ $totalLessons }} lessons completed
                            </div>
                        </div>

                        <div class="mt-auto flex items-center justify-between gap-3">
                            <div class="text-xs text-gray-600 truncate">
                                @if($course->teacher)
                                    Instructor: {{ $course->teacher->name }}
                                @endif
                            </div>
                            <a href="{{ route('student.learning.index', $course->id) }}"
                               class="text-sm text-white bg-blue-600 hover:bg-blue-700 px-3 py-2 rounded-lg whitespace-nowrap">
                                Continue
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
