@extends('layouts.teacher')

@section('title', 'Course: ' . $course->title)
@section('page-title', $course->title)

@push('styles')
<style>
    .hierarchy-item {
        transition: all 0.2s ease;
    }
    .hierarchy-item:hover {
        background-color: #f9fafb;
    }
    .chapter-card {
        border-left: 4px solid #3b82f6;
    }
    .lesson-item {
        border-left: 3px solid #60a5fa;
    }
    .topic-item {
        border-left: 2px solid #93c5fd;
    }
    .modal-overlay {
        backdrop-filter: blur(4px);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 lg:px-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-4">
            <div class="flex-1 min-w-0">
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-2 break-words">{{ $course->title }}</h1>
                <div class="flex flex-wrap items-center gap-2 text-xs sm:text-sm text-gray-600">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        {{ $course->subject->grade->name ?? 'N/A' }}
                    </span>
                    <span class="hidden sm:inline">→</span>
                    <span>{{ $course->subject->name ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 shrink-0">
                <a href="{{ route('teacher.courses.edit', $course->id) }}"
                   class="inline-flex items-center justify-center bg-blue-600 text-white px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg hover:bg-blue-700 transition-colors font-medium text-xs sm:text-sm whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Course
                </a>
                <a href="{{ route('teacher.courses.index') }}"
                   class="inline-flex items-center justify-center bg-gray-100 text-gray-700 px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg hover:bg-gray-200 transition-colors font-medium text-xs sm:text-sm whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Courses
                </a>
            </div>
        </div>
    </div>

    <!-- Course Overview Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
        <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Course Overview
        </h2>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-4">
            <!-- Status Card -->
            <div class="flex flex-col items-center sm:items-start gap-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-center sm:text-left w-full">
                    <p class="text-xs font-medium text-gray-500 mb-0.5">Status</p>
                    <p class="text-xs font-semibold">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : ($course->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($course->status) }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Price Card -->
            <div class="flex flex-col items-center sm:items-start gap-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-center sm:text-left w-full">
                    <p class="text-xs font-medium text-gray-500 mb-0.5">Price</p>
                    <p class="text-xs sm:text-sm font-bold">
                        @if($course->is_free)
                            <span class="text-green-600">Free</span>
                        @else
                            <span class="text-gray-900">Rs. {{ number_format($course->price, 0) }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Access Duration Card -->
            <div class="flex flex-col items-center sm:items-start gap-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-center sm:text-left w-full">
                    <p class="text-xs font-medium text-gray-500 mb-0.5">Duration</p>
                    <p class="text-xs sm:text-sm font-bold text-gray-900">{{ $course->access_duration_months ?? $course->duration_months ?? 'N/A' }}m</p>
                </div>
            </div>

            <!-- Chapters Count -->
            <div class="flex flex-col items-center sm:items-start gap-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="text-center sm:text-left w-full">
                    <p class="text-xs font-medium text-gray-500 mb-0.5">Chapters</p>
                    <p class="text-xs sm:text-sm font-bold text-gray-900">{{ $course->chapters->count() ?? 0 }}</p>
                </div>
            </div>

            <!-- Lessons Count -->
            <div class="flex flex-col items-center sm:items-start gap-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="text-center sm:text-left w-full">
                    <p class="text-xs font-medium text-gray-500 mb-0.5">Lessons</p>
                    <p class="text-xs sm:text-sm font-bold text-gray-900">{{ $course->chapters->sum(function($chapter) { return $chapter->lessons->count(); }) ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Additional Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
            @if($course->description)
            <div class="sm:col-span-2 lg:col-span-3">
                <h3 class="text-xs font-semibold text-gray-700 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                    </svg>
                    Description
                </h3>
                <p class="text-xs sm:text-sm text-gray-700 line-clamp-3">{{ $course->description }}</p>
            </div>
            @endif

            @if($course->difficulty_level)
            <div>
                <h3 class="text-xs font-semibold text-gray-700 mb-1.5 flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Difficulty
                </h3>
                <p class="text-xs sm:text-sm text-gray-900 capitalize">{{ $course->difficulty_level }}</p>
            </div>
            @endif

            @if($course->language)
            <div>
                <h3 class="text-xs font-semibold text-gray-700 mb-1.5 flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                    </svg>
                    Language
                </h3>
                <p class="text-xs sm:text-sm text-gray-900">
                    @php
                        $languages = [
                            'en' => 'English',
                            'ur' => 'Urdu',
                            'ar' => 'Arabic',
                            'other' => 'Other'
                        ];
                        echo $languages[$course->language] ?? ucfirst($course->language);
                    @endphp
                </p>
            </div>
            @endif

            @if($course->short_description)
            <div class="sm:col-span-2 lg:col-span-1">
                <h3 class="text-xs font-semibold text-gray-700 mb-1.5 flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10m-7 4h7"></path>
                    </svg>
                    Short Description
                </h3>
                <p class="text-xs sm:text-sm text-gray-700">{{ $course->short_description }}</p>
            </div>
            @endif
        </div>

        <!-- Learning Objectives -->
        @if($course->learning_objectives && is_array($course->learning_objectives) && count($course->learning_objectives) > 0)
            <div class="border-t pt-4 mt-4">
                <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-3 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    What Students Will Learn
                </h3>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                    @foreach($course->learning_objectives as $objective)
                        @if(!empty(trim($objective)))
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-xs sm:text-sm text-gray-700 break-words">{{ trim($objective) }}</span>
                        </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @elseif($course->what_you_will_learn)
            <div class="border-t pt-4 mt-4">
                <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-3 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    What Students Will Learn
                </h3>
                <div class="text-xs sm:text-sm text-gray-700 whitespace-pre-line bg-gray-50 p-3 sm:p-4 rounded-lg break-words">{{ $course->what_you_will_learn }}</div>
            </div>
        @endif
    </div>

    <!-- Course Content Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6 gap-3 sm:gap-4">
            <div class="flex-1 min-w-0">
                <h2 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="break-words">Course Content Structure</span>
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Organize your course into chapters, lessons, and topics</p>
            </div>
            <button onclick="showAddChapterModal()"
                    class="inline-flex items-center justify-center bg-blue-600 text-white px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg hover:bg-blue-700 transition-colors font-medium text-xs sm:text-sm whitespace-nowrap shrink-0">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Chapter
            </button>
        </div>

        <!-- Course Structure -->
        <div class="space-y-4">
            @forelse($course->chapters as $chapter)
                <div class="chapter-card bg-gray-50 rounded-lg p-3 sm:p-4 border border-gray-200">
                    <!-- Chapter Header -->
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-1">
                                <div class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-blue-600 text-white font-bold text-xs sm:text-sm shrink-0">
                                    {{ $loop->iteration }}
                                </div>
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900 break-words flex-1 min-w-0">{{ $chapter->title }}</h3>
                                @if($chapter->is_free)
                                    <span class="inline-flex items-center px-2 py-0.5 sm:py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 shrink-0">
                                        FREE
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 sm:py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 shrink-0">
                                        PAID
                                    </span>
                                @endif
                            </div>
                            @if($chapter->description)
                                <p class="text-xs sm:text-sm text-gray-600 mt-2 break-words">{{ $chapter->description }}</p>
                            @endif
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <button type="button"
                                    class="edit-chapter-btn inline-flex items-center px-2 sm:px-3 py-1.5 text-xs sm:text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors whitespace-nowrap"
                                    data-chapter-id="{{ $chapter->id }}"
                                    data-chapter-title="{{ htmlspecialchars($chapter->title ?? '', ENT_QUOTES, 'UTF-8') }}"
                                    data-chapter-description="{{ htmlspecialchars($chapter->description ?? '', ENT_QUOTES, 'UTF-8') }}"
                                    data-chapter-is-free="{{ $chapter->is_free ? '1' : '0' }}"
                                    data-chapter-order="{{ $chapter->order ?? 0 }}">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                <span class="hidden sm:inline">Edit</span>
                            </button>
                            <form action="{{ route('teacher.courses.chapters.destroy', ['bookId' => $course->id, 'chapterId' => $chapter->id]) }}"
                                  method="POST" onsubmit="return confirm('Are you sure? This will delete all lessons and topics in this chapter.')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-2 sm:px-3 py-1.5 text-xs sm:text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors whitespace-nowrap">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    <span class="hidden sm:inline">Delete</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Lessons in Chapter -->
                    <div class="space-y-3 mt-3">
                        @forelse($chapter->lessons as $lesson)
                            <div class="lesson-item bg-white rounded-lg p-2 sm:p-3 border border-gray-200">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">
                                    <div class="flex flex-wrap items-center gap-2 flex-1 min-w-0">
                                        <div class="flex items-center justify-center w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-blue-100 text-blue-700 font-semibold text-xs shrink-0">
                                            {{ $loop->iteration }}
                                        </div>
                                        <span class="text-xs sm:text-sm font-medium text-gray-900 break-words flex-1 min-w-0">{{ $lesson->title }}</span>
                                        @if($lesson->is_free)
                                            <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 shrink-0">
                                                FREE
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 shrink-0">
                                                PAID
                                            </span>
                                        @endif
                                        <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded text-xs font-medium {{ $lesson->status === 'published' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }} shrink-0">
                                            {{ ucfirst($lesson->status) }}
                                        </span>
                                    </div>
                                    <div class="flex gap-2 shrink-0">
                                        <button onclick="editLesson({{ $lesson->id }}, {!! json_encode($lesson->title) !!}, {!! json_encode($lesson->description ?? '') !!}, {{ $lesson->is_free ? 'true' : 'false' }}, {{ $lesson->order }}, {!! json_encode($lesson->status) !!}, {{ $chapter->id }})"
                                                class="text-xs text-blue-600 hover:text-blue-700 hover:bg-blue-50 px-2 py-1 rounded transition-colors whitespace-nowrap"
                                                type="button">
                                            Edit
                                        </button>
                                        <form action="{{ route('teacher.courses.chapters.lessons.destroy', ['bookId' => $course->id, 'chapterId' => $chapter->id, 'lessonId' => $lesson->id]) }}"
                                              method="POST" onsubmit="return confirm('Are you sure? This will delete all topics in this lesson.');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-700 hover:bg-red-50 px-2 py-1 rounded transition-colors whitespace-nowrap">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Topics in Lesson -->
                                @if($lesson->topics && $lesson->topics->count() > 0)
                                    <div class="space-y-2 mt-2 pl-4 sm:pl-6">
                                        @foreach($lesson->topics as $topic)
                                            <div class="topic-item bg-gray-50 rounded p-2 border border-gray-200">
                                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                                    <div class="flex flex-wrap items-center gap-2 flex-1 min-w-0">
                                                        <span class="text-gray-400 shrink-0">•</span>
                                                        <span class="text-xs text-gray-700 font-medium break-words flex-1 min-w-0">{{ $topic->title }}</span>
                                                        @if($topic->type)
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 shrink-0">
                                                                {{ strtoupper($topic->type) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="flex gap-2 shrink-0">
                                                        <button onclick="editTopic({{ $topic->id }}, {!! json_encode($topic->title) !!}, {!! json_encode($topic->description ?? '') !!}, {{ $topic->is_free ? 'true' : 'false' }}, {{ $topic->order }}, {!! json_encode($topic->type) !!}, {!! json_encode($topic->video_host ?? '') !!}, {!! json_encode($topic->video_id ?? '') !!}, {{ $lesson->id }}, {{ $chapter->id }})"
                                                                class="text-xs text-blue-500 hover:text-blue-700 whitespace-nowrap">
                                                            Edit
                                                        </button>
                                                        <form action="{{ route('teacher.courses.chapters.lessons.topics.destroy', ['bookId' => $course->id, 'chapterId' => $chapter->id, 'lessonId' => $lesson->id, 'topicId' => $topic->id]) }}"
                                                              method="POST" onsubmit="return confirm('Are you sure?')" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 whitespace-nowrap">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <button onclick="showAddTopicModal({{ $lesson->id }}, {{ $chapter->id }})"
                                        class="text-xs text-blue-600 hover:text-blue-700 hover:bg-blue-50 px-2 py-1 rounded mt-2 inline-flex items-center transition-colors whitespace-nowrap">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Topic
                                </button>
                            </div>
                        @empty
                            <div class="bg-white rounded-lg p-2 sm:p-3 border border-dashed border-gray-300 text-center">
                                <p class="text-xs sm:text-sm text-gray-500">No lessons yet</p>
                            </div>
                        @endforelse

                        <button onclick="showAddLessonModal({{ $chapter->id }})"
                                class="inline-flex items-center text-xs sm:text-sm text-blue-600 hover:text-blue-700 hover:bg-blue-50 px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg transition-colors font-medium mt-2">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Lesson
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 sm:py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 px-4">
                    <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">No chapters yet</h3>
                    <p class="text-xs sm:text-sm text-gray-600 mb-4">Start building your course by adding your first chapter</p>
                    <button onclick="showAddChapterModal()"
                            class="inline-flex items-center bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium text-xs sm:text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Your First Chapter
                    </button>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Include Modals (keeping the existing modal code but with improved styling) -->
@include('teacher.courses.partials.chapter-modals')
@include('teacher.courses.partials.lesson-modals')
@include('teacher.courses.partials.topic-modals')

@push('scripts')
<script>
function showAddChapterModal() {
    const modal = document.getElementById('addChapterModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'block';
        // Prevent background scrolling
        document.body.style.overflow = 'hidden';
        document.body.style.paddingRight = '0px'; // Prevent layout shift
        // Lock the body
        document.documentElement.style.overflow = 'hidden';
    }
}

function editChapter(id, title, description, isFree, order) {
    try {
        console.log('editChapter called with:', {id, title, description, isFree, order});

        const titleInput = document.getElementById('edit_chapter_title');
        const descInput = document.getElementById('edit_chapter_description');
        const orderInput = document.getElementById('edit_chapter_order');
        const isFreeCheckbox = document.getElementById('edit_chapter_is_free');
        const form = document.getElementById('editChapterForm');
        const modal = document.getElementById('editChapterModal');

        if (!titleInput || !descInput || !orderInput || !isFreeCheckbox || !form || !modal) {
            console.error('Required elements not found:', {
                titleInput: !!titleInput,
                descInput: !!descInput,
                orderInput: !!orderInput,
                isFreeCheckbox: !!isFreeCheckbox,
                form: !!form,
                modal: !!modal
            });
            alert('Error: Edit form elements not found. Please refresh the page.');
            return;
        }

        titleInput.value = title || '';
        descInput.value = description || '';
        orderInput.value = order || 0;
        isFreeCheckbox.checked = isFree === true || isFree === 'true' || isFree === 1 || isFree === '1';
        form.action = '{{ route("teacher.courses.chapters.update", ["bookId" => $course->id, "chapterId" => ":id"]) }}'.replace(':id', id);

        modal.classList.remove('hidden');
        modal.style.display = 'block';
        // Prevent background scrolling
        document.body.style.overflow = 'hidden';
        document.body.style.paddingRight = '0px';
        document.documentElement.style.overflow = 'hidden';
    } catch (error) {
        console.error('Error in editChapter:', error);
        alert('An error occurred while opening the edit form. Please check the console for details.');
    }
}

function showAddLessonModal(chapterId) {
    document.getElementById('addLessonForm').action = '{{ route("teacher.courses.chapters.lessons.store", ["bookId" => $course->id, "chapterId" => ":id"]) }}'.replace(':id', chapterId);
    const modal = document.getElementById('addLessonModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'block';
        // Prevent background scrolling
        document.body.style.overflow = 'hidden';
        document.body.style.paddingRight = '0px';
        document.documentElement.style.overflow = 'hidden';
        document.getElementById('addLessonForm').reset();
    }
}

function editLesson(lessonId, title, description, isFree, order, status, chapterId) {
    try {
        document.getElementById('edit_lesson_title').value = title || '';
        document.getElementById('edit_lesson_description').value = description || '';
        document.getElementById('edit_lesson_order').value = order || 0;
        document.getElementById('edit_lesson_is_free').checked = isFree === true || isFree === 'true' || isFree === 1;
        document.getElementById('edit_lesson_status').value = status || 'draft';
        document.getElementById('editLessonForm').action = '{{ route("teacher.courses.chapters.lessons.update", ["bookId" => $course->id, "chapterId" => ":chapterId", "lessonId" => ":lessonId"]) }}'
            .replace(':chapterId', chapterId)
            .replace(':lessonId', lessonId);
        const modal = document.getElementById('editLessonModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'block';
            // Prevent background scrolling
            document.body.style.overflow = 'hidden';
            document.body.style.paddingRight = '0px';
            document.documentElement.style.overflow = 'hidden';
        }
    } catch (error) {
        console.error('Error in editLesson:', error);
        alert('An error occurred while opening the edit form. Please check the console for details.');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        // Restore background scrolling
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        document.documentElement.style.overflow = '';
    }
}

function showAddTopicModal(lessonId, chapterId) {
    document.getElementById('addTopicForm').action = '{{ route("teacher.courses.chapters.lessons.topics.store", ["bookId" => $course->id, "chapterId" => ":chapterId", "lessonId" => ":lessonId"]) }}'
        .replace(':chapterId', chapterId)
        .replace(':lessonId', lessonId);
    const modal = document.getElementById('addTopicModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'block';
        // Prevent background scrolling
        document.body.style.overflow = 'hidden';
        document.body.style.paddingRight = '0px';
        document.documentElement.style.overflow = 'hidden';
        document.getElementById('addTopicForm').reset();
        toggleVideoInputs('add');
    }
}

function editTopic(topicId, title, description, isFree, order, type, videoHost, videoId, lessonId, chapterId) {
    document.getElementById('edit_topic_title').value = title;
    document.getElementById('edit_topic_description').value = description || '';
    document.getElementById('edit_topic_order').value = order;
    document.getElementById('edit_topic_type').value = type;
    document.getElementById('edit_topic_is_free').checked = isFree;
    document.getElementById('edit_topic_video_host').value = videoHost || '';
    if (videoHost === 'youtube' || videoHost === 'bunny') {
        document.getElementById('edit_topic_video_id').value = videoId || '';
        if (videoHost === 'bunny') {
            document.getElementById('edit_topic_bunny_video_id').value = videoId || '';
        }
    }
    document.getElementById('editTopicForm').action = '{{ route("teacher.courses.chapters.lessons.topics.update", ["bookId" => $course->id, "chapterId" => ":chapterId", "lessonId" => ":lessonId", "topicId" => ":topicId"]) }}'
        .replace(':chapterId', chapterId)
        .replace(':lessonId', lessonId)
        .replace(':topicId', topicId);
    const modal = document.getElementById('editTopicModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'block';
        // Prevent background scrolling
        document.body.style.overflow = 'hidden';
        document.body.style.paddingRight = '0px';
        document.documentElement.style.overflow = 'hidden';
        toggleVideoInputs('edit');
    }
}

function toggleVideoInputs(mode) {
    const prefix = mode === 'add' ? 'add_topic' : 'edit_topic';
    const videoHost = document.getElementById(prefix + '_video_host').value;

    document.getElementById(prefix + '_youtube_input').classList.add('hidden');
    document.getElementById(prefix + '_bunny_input').classList.add('hidden');
    document.getElementById(prefix + '_upload_input').classList.add('hidden');

    if (videoHost === 'youtube') {
        document.getElementById(prefix + '_youtube_input').classList.remove('hidden');
    } else if (videoHost === 'bunny') {
        document.getElementById(prefix + '_bunny_input').classList.remove('hidden');
    } else if (videoHost === 'upload') {
        document.getElementById(prefix + '_upload_input').classList.remove('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Handle edit chapter buttons with data attributes - use event delegation
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.edit-chapter-btn');
        if (button) {
            e.preventDefault();
            e.stopPropagation();
            const chapterId = button.getAttribute('data-chapter-id');
            const title = button.getAttribute('data-chapter-title');
            const description = button.getAttribute('data-chapter-description');
            const isFree = button.getAttribute('data-chapter-is-free') === '1';
            const order = parseInt(button.getAttribute('data-chapter-order')) || 0;

            console.log('Edit chapter button clicked:', {chapterId, title, description, isFree, order});

            if (chapterId) {
                editChapter(chapterId, title, description, isFree, order);
            } else {
                console.error('Chapter ID not found on button');
            }
        }
    });

    // Prevent body scroll when modal is open
    const modals = ['addLessonModal', 'editLessonModal', 'addChapterModal', 'editChapterModal', 'addTopicModal', 'editTopicModal'];

    // Handle ESC key to close modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && !modal.classList.contains('hidden')) {
                    closeModal(modalId);
                }
            });
        }
    });

    // Handle click outside modal to close
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                // Close if clicking on the backdrop (the modal container itself)
                if (e.target === modal || e.target.classList.contains('bg-black')) {
                    closeModal(modalId);
                }
            });
        }
    });
});
</script>
@endpush
@endsection
