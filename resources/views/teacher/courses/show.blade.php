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
            <div class="flex flex-row sm:flex-row sm:items-center gap-2 shrink-0 w-full sm:w-auto">
                <a href="{{ route('teacher.courses.index') }}"
                   class="flex items-center justify-center h-11 w-full sm:w-auto min-w-0 px-3 sm:px-4 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors font-medium text-xs sm:text-sm whitespace-nowrap"
                   style="text-decoration: none;">
                    <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Courses
                </a>
                <a href="{{ route('teacher.courses.edit', $course->id) }}"
                   class="flex items-center justify-center h-11 w-full sm:w-auto min-w-0 px-3 sm:px-4 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors font-medium text-xs sm:text-sm whitespace-nowrap"
                   style="text-decoration: none;">
                    <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Course
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
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2 sm:gap-3 mb-4">
            <!-- Status Card -->
            <div class="flex flex-col items-center sm:items-start gap-1.5 p-2 sm:p-2.5 bg-gray-50 rounded-lg border border-gray-200">
                <div class="w-8 h-8 rounded-md bg-blue-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-center sm:text-left w-full min-w-0">
                    <p class="text-[11px] sm:text-xs font-medium text-gray-500 truncate">Status</p>
                    <p class="text-xs font-semibold truncate">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[11px] font-medium {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : ($course->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($course->status) }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Price Card -->
            <div class="flex flex-col items-center sm:items-start gap-1.5 p-2 sm:p-2.5 bg-gray-50 rounded-lg border border-gray-200">
                <div class="w-8 h-8 rounded-md bg-green-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-center sm:text-left w-full min-w-0">
                    <p class="text-[11px] sm:text-xs font-medium text-gray-500 truncate">Price</p>
                    <p class="text-xs font-bold truncate">
                        @if($course->is_free)
                            <span class="text-green-600">Free</span>
                        @else
                            <span class="text-gray-900">Rs. {{ number_format($course->price, 0) }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Access Duration Card -->
            <div class="flex flex-col items-center sm:items-start gap-1.5 p-2 sm:p-2.5 bg-gray-50 rounded-lg border border-gray-200">
                <div class="w-8 h-8 rounded-md bg-purple-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-center sm:text-left w-full min-w-0">
                    <p class="text-[11px] sm:text-xs font-medium text-gray-500 truncate">Duration</p>
                    <p class="text-xs font-bold text-gray-900 truncate">{{ $course->access_duration_months ?? $course->duration_months ?? 'N/A' }}m</p>
                </div>
            </div>

            <!-- Chapters Count -->
            <div class="flex flex-col items-center sm:items-start gap-1.5 p-2 sm:p-2.5 bg-gray-50 rounded-lg border border-gray-200">
                <div class="w-8 h-8 rounded-md bg-indigo-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="text-center sm:text-left w-full min-w-0">
                    <p class="text-[11px] sm:text-xs font-medium text-gray-500 truncate">Chapters</p>
                    <p class="text-xs font-bold text-gray-900 truncate">{{ $course->chapters->count() ?? 0 }}</p>
                </div>
            </div>

            <!-- Lessons Count -->
            <div class="flex flex-col items-center sm:items-start gap-1.5 p-2 sm:p-2.5 bg-gray-50 rounded-lg border border-gray-200">
                <div class="w-8 h-8 rounded-md bg-orange-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="text-center sm:text-left w-full min-w-0">
                    <p class="text-[11px] sm:text-xs font-medium text-gray-500 truncate">Lessons</p>
                    <p class="text-xs font-bold text-gray-900 truncate">{{ $course->chapters->sum(function($chapter) { return $chapter->lessons->count(); }) ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Additional Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5 mb-4">
            @if($course->description)
            <div class="sm:col-span-2 lg:col-span-3">
                <h3 class="text-sm font-semibold text-gray-800 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                    </svg>
                    Description
                </h3>
                <p class="text-sm text-gray-700 leading-relaxed line-clamp-4">{{ $course->description }}</p>
            </div>
            @endif

            @if($course->difficulty_level)
            <div class="min-w-0">
                <h3 class="text-sm font-semibold text-gray-800 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Difficulty
                </h3>
                <p class="text-sm text-gray-900 capitalize leading-snug">{{ $course->difficulty_level }}</p>
            </div>
            @endif

            @if($course->language)
            <div class="min-w-0">
                <h3 class="text-sm font-semibold text-gray-800 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                    </svg>
                    Language
                </h3>
                <p class="text-sm text-gray-900 leading-snug">
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
            <div class="sm:col-span-2 lg:col-span-1 min-w-0">
                <h3 class="text-sm font-semibold text-gray-800 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10m-7 4h7"></path>
                    </svg>
                    Short Description
                </h3>
                <p class="text-sm text-gray-700 leading-snug">{{ $course->short_description }}</p>
            </div>
            @endif
        </div>

        <!-- Learning Objectives -->
        @if($course->learning_objectives && is_array($course->learning_objectives) && count($course->learning_objectives) > 0)
            <div class="border-t border-gray-200 pt-5 mt-5">
                <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    What Students Will Learn
                </h3>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3" role="list">
                    @foreach($course->learning_objectives as $objective)
                        @if(!empty(trim($objective)))
                        <li class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm text-gray-700 leading-snug break-words">{{ trim($objective) }}</span>
                        </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @elseif($course->what_you_will_learn)
            <div class="border-t border-gray-200 pt-5 mt-5">
                <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    What Students Will Learn
                </h3>
                <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line bg-gray-50 border border-gray-100 p-4 rounded-lg">{{ $course->what_you_will_learn }}</div>
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
                    <!-- Chapter Header: number, title, FREE/PAID, Edit, Delete in one line -->
                    <div class="mb-3">
                        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 sm:gap-3">
                            <div class="flex-none flex items-center justify-center w-8 h-8 min-w-8 min-h-8 rounded-full bg-blue-600 text-white text-sm font-bold tabular-nums shrink-0">
                                {{ $loop->iteration }}
                            </div>
                            <h3 class="flex-1 min-w-0 text-base sm:text-lg font-semibold text-gray-900 truncate">{{ $chapter->title }}</h3>
                            @if($chapter->is_free)
                                <span class="inline-flex items-center px-2 py-0.5 sm:py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 shrink-0">FREE</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 sm:py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 shrink-0">PAID</span>
                            @endif
                            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                                <button type="button"
                                        class="edit-chapter-btn inline-flex items-center justify-center h-8 w-8 rounded-lg text-blue-600 hover:text-blue-700 hover:bg-blue-50 transition-colors"
                                        data-chapter-id="{{ $chapter->id }}"
                                        data-chapter-title="{{ htmlspecialchars($chapter->title ?? '', ENT_QUOTES, 'UTF-8') }}"
                                        data-chapter-description="{{ htmlspecialchars($chapter->description ?? '', ENT_QUOTES, 'UTF-8') }}"
                                        data-chapter-is-free="{{ $chapter->is_free ? '1' : '0' }}"
                                        data-chapter-order="{{ $chapter->order ?? 0 }}"
                                        title="Edit chapter" aria-label="Edit chapter">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <form action="{{ route('teacher.courses.chapters.destroy', ['bookId' => $course->id, 'chapterId' => $chapter->id]) }}"
                                      method="POST" onsubmit="return confirm('Are you sure? This will delete all lessons and topics in this chapter.')" class="inline m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-red-600 hover:text-red-700 hover:bg-red-50 transition-colors"
                                            title="Delete chapter" aria-label="Delete chapter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if($chapter->description)
                            <p class="text-xs sm:text-sm text-gray-600 mt-2 break-words">{{ $chapter->description }}</p>
                        @endif
                    </div>

                    <!-- Lessons in Chapter -->
                    <div class="space-y-2 mt-2">
                        @forelse($chapter->lessons as $lesson)
                            <div class="lesson-item bg-white rounded-lg p-2 border border-gray-200">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">
                                    <div class="flex flex-wrap items-center gap-2 flex-1 min-w-0">
                                        <div class="flex-none flex items-center justify-center w-6 h-6 min-w-6 min-h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold tabular-nums shrink-0">
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
                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 shrink-0">
                                        <button type="button"
                                            class="edit-lesson-btn inline-flex items-center justify-center h-8 w-8 rounded-lg text-blue-600 hover:text-blue-700 hover:bg-blue-50 transition-colors"
                                                data-lesson-id="{{ $lesson->id }}"
                                                data-lesson-title="{{ htmlspecialchars($lesson->title ?? '', ENT_QUOTES, 'UTF-8') }}"
                                                data-lesson-description="{{ htmlspecialchars($lesson->description ?? '', ENT_QUOTES, 'UTF-8') }}"
                                                data-lesson-is-free="{{ $lesson->is_free ? '1' : '0' }}"
                                                data-lesson-order="{{ $lesson->order ?? 0 }}"
                                                data-lesson-status="{{ htmlspecialchars($lesson->status ?? 'draft', ENT_QUOTES, 'UTF-8') }}"
                                                data-chapter-id="{{ $chapter->id }}"
                                                title="Edit lesson" aria-label="Edit lesson">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <form action="{{ route('teacher.courses.chapters.lessons.destroy', ['bookId' => $course->id, 'chapterId' => $chapter->id, 'lessonId' => $lesson->id]) }}"
                                            method="POST" onsubmit="return confirm('Are you sure? This will delete all topics in this lesson.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-red-600 hover:text-red-700 hover:bg-red-50 transition-colors"
                                                    title="Delete lesson" aria-label="Delete lesson">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Topics in Lesson -->
                                @if($lesson->topics && $lesson->topics->count() > 0)
                                    <div class="space-y-1.5 mt-1.5 pl-4 sm:pl-6">
                                        @foreach($lesson->topics as $topic)
                                            <div class="topic-item bg-gray-50 rounded p-1.5 sm:p-2 border border-gray-200">
                                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1.5 sm:gap-2">
                                                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 flex-1 min-w-0">
                                                        <span class="text-gray-400 shrink-0 leading-none">•</span>
                                                        <span class="text-xs sm:text-sm text-gray-700 font-medium break-words flex-1 min-w-0">
                                                            {{ $topic->title }}
                                                        </span>
                                                        @if($topic->type)
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-medium bg-purple-100 text-purple-800 shrink-0">
                                                                {{ strtoupper($topic->type) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 shrink-0">
                                                        <button type="button"
                                                                class="edit-topic-btn inline-flex items-center justify-center h-8 w-8 rounded-lg text-blue-600 hover:text-blue-700 hover:bg-blue-50 transition-colors"
                                                                data-topic-id="{{ $topic->id }}"
                                                                data-topic-title="{{ htmlspecialchars($topic->title ?? '', ENT_QUOTES, 'UTF-8') }}"
                                                                data-topic-description="{{ htmlspecialchars($topic->description ?? '', ENT_QUOTES, 'UTF-8') }}"
                                                                data-topic-is-free="{{ $topic->is_free ? '1' : '0' }}"
                                                                data-topic-order="{{ $topic->order ?? 0 }}"
                                                                data-topic-type="{{ htmlspecialchars($topic->type ?? '', ENT_QUOTES, 'UTF-8') }}"
                                                                data-topic-video-host="{{ htmlspecialchars($topic->video_host ?? '', ENT_QUOTES, 'UTF-8') }}"
                                                                data-topic-video-id="{{ htmlspecialchars($topic->video_id ?? '', ENT_QUOTES, 'UTF-8') }}"
                                                                data-lesson-id="{{ $lesson->id }}"
                                                                data-chapter-id="{{ $chapter->id }}"
                                                                title="Edit topic" aria-label="Edit topic">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </button>
                                                        <form action="{{ route('teacher.courses.chapters.lessons.topics.destroy', ['bookId' => $course->id, 'chapterId' => $chapter->id, 'lessonId' => $lesson->id, 'topicId' => $topic->id]) }}"
                                                              method="POST" onsubmit="return confirm('Are you sure?')" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-red-600 hover:text-red-700 hover:bg-red-50 transition-colors"
                                                                    title="Delete topic" aria-label="Delete topic">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <button onclick="showAddTopicModal({{ $lesson->id }}, {{ $chapter->id }})"
                                        class="inline-flex items-center justify-center gap-1.5 h-9 min-h-9 px-3 rounded-lg text-xs font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 transition-colors whitespace-nowrap mt-2">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                class="inline-flex items-center justify-center gap-1.5 h-9 min-h-9 px-3 rounded-lg text-xs font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 transition-colors whitespace-nowrap mt-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
// Bootstrap Modal Helper Functions
function showBootstrapModal(modalId) {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }
}

function hideBootstrapModal(modalId) {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    }
}

function showAddChapterModal() {
    showBootstrapModal('addChapterModal');
}

function editChapter(id, title, description, isFree, order) {
    try {
        const titleInput = document.getElementById('edit_chapter_title');
        const descInput = document.getElementById('edit_chapter_description');
        const orderInput = document.getElementById('edit_chapter_order');
        const isFreeCheckbox = document.getElementById('edit_chapter_is_free');
        const form = document.getElementById('editChapterForm');

        if (!titleInput || !descInput || !orderInput || !isFreeCheckbox || !form) {
            console.error('Required elements not found');
            alert('Error: Edit form elements not found. Please refresh the page.');
            return;
        }

        titleInput.value = title || '';
        descInput.value = description || '';
        orderInput.value = order || 0;
        isFreeCheckbox.checked = isFree === true || isFree === 'true' || isFree === 1 || isFree === '1';
        form.action = '{{ route("teacher.courses.chapters.update", ["bookId" => $course->id, "chapterId" => ":id"]) }}'.replace(':id', id);

        showBootstrapModal('editChapterModal');
    } catch (error) {
        console.error('Error in editChapter:', error);
        alert('An error occurred while opening the edit form. Please check the console for details.');
    }
}

function showAddLessonModal(chapterId) {
    document.getElementById('addLessonForm').action = '{{ route("teacher.courses.chapters.lessons.store", ["bookId" => $course->id, "chapterId" => ":id"]) }}'.replace(':id', chapterId);
    document.getElementById('addLessonForm').reset();
    showBootstrapModal('addLessonModal');
}

function editLesson(lessonId, title, description, isFree, order, status, chapterId) {
    try {
        const titleInput = document.getElementById('edit_lesson_title');
        const descInput = document.getElementById('edit_lesson_description');
        const orderInput = document.getElementById('edit_lesson_order');
        const isFreeCheckbox = document.getElementById('edit_lesson_is_free');
        const statusSelect = document.getElementById('edit_lesson_status');
        const form = document.getElementById('editLessonForm');

        if (!titleInput || !descInput || !orderInput || !isFreeCheckbox || !statusSelect || !form) {
            console.error('Required elements not found');
            alert('Error: Edit form elements not found. Please refresh the page.');
            return;
        }

        titleInput.value = title || '';
        descInput.value = description || '';
        orderInput.value = order || 0;
        isFreeCheckbox.checked = isFree === true || isFree === 'true' || isFree === 1 || isFree === '1';
        statusSelect.value = status || 'draft';
        form.action = '{{ route("teacher.courses.chapters.lessons.update", ["bookId" => $course->id, "chapterId" => ":chapterId", "lessonId" => ":lessonId"]) }}'
            .replace(':chapterId', chapterId)
            .replace(':lessonId', lessonId);

        showBootstrapModal('editLessonModal');
    } catch (error) {
        console.error('Error in editLesson:', error);
        alert('An error occurred while opening the edit form. Please check the console for details.');
    }
}

function showAddTopicModal(lessonId, chapterId) {
    document.getElementById('addTopicForm').action = '{{ route("teacher.courses.chapters.lessons.topics.store", ["bookId" => $course->id, "chapterId" => ":chapterId", "lessonId" => ":lessonId"]) }}'
        .replace(':chapterId', chapterId)
        .replace(':lessonId', lessonId);
    document.getElementById('addTopicForm').reset();
    toggleVideoInputs('add');
    showBootstrapModal('addTopicModal');
}

function editTopic(topicId, title, description, isFree, order, type, videoHost, videoId, lessonId, chapterId) {
    try {
        const titleInput = document.getElementById('edit_topic_title');
        const descInput = document.getElementById('edit_topic_description');
        const orderInput = document.getElementById('edit_topic_order');
        const typeSelect = document.getElementById('edit_topic_type');
        const isFreeCheckbox = document.getElementById('edit_topic_is_free');
        const videoHostSelect = document.getElementById('edit_topic_video_host');
        const form = document.getElementById('editTopicForm');

        if (!titleInput || !descInput || !orderInput || !typeSelect || !isFreeCheckbox || !videoHostSelect || !form) {
            console.error('Required elements not found');
            alert('Error: Edit form elements not found. Please refresh the page.');
            return;
        }

        titleInput.value = title || '';
        descInput.value = description || '';
        orderInput.value = order || 0;
        typeSelect.value = ['lecture','quiz','mcq'].includes(String(type)) ? type : 'lecture';
        isFreeCheckbox.checked = isFree === true || isFree === 'true' || isFree === 1 || isFree === '1';

        // Set video host first
        videoHostSelect.value = videoHost || '';

        // Set form action URL (must be set before submit so the form posts to the correct update route)
        form.action = '{{ route("teacher.courses.chapters.lessons.topics.update", ["bookId" => $course->id, "chapterId" => ":chapterId", "lessonId" => ":lessonId", "topicId" => ":topicId"]) }}'
            .replace(':chapterId', String(chapterId))
            .replace(':lessonId', String(lessonId))
            .replace(':topicId', String(topicId));

        // Show modal first
        const modalElement = document.getElementById('editTopicModal');
        const modal = new bootstrap.Modal(modalElement);

        // Store video data for later use
        const videoData = { videoHost, videoId };
        console.log('Stored video data for edit:', videoData);

        // Wait for modal to be fully shown before setting values
        modalElement.addEventListener('shown.bs.modal', function onModalShown() {
            // Remove listener after first use
            modalElement.removeEventListener('shown.bs.modal', onModalShown);

            console.log('Modal shown, setting video data:', videoData);

            // Toggle video inputs to show the correct input field
            toggleVideoInputs('edit');

            // Function to set video ID with retries
            const setVideoId = (retryCount = 0) => {
                const maxRetries = 5;

                if (videoData.videoHost === 'youtube') {
                    const videoIdInput = document.getElementById('edit_topic_video_id');
                    const youtubeInputContainer = document.getElementById('edit_topic_youtube_input');

                    // Check if input is visible
                    const isVisible = youtubeInputContainer &&
                        window.getComputedStyle(youtubeInputContainer).display !== 'none';

                    if (videoIdInput && isVisible) {
                        // Set the video ID value (could be just ID or full URL)
                        videoIdInput.value = videoData.videoId || '';
                        console.log('✓ Set YouTube video ID/URL:', videoData.videoId);
                        console.log('✓ Video ID input value:', videoIdInput.value);

                        // Trigger input event to show preview
                        if (videoData.videoId) {
                            videoIdInput.dispatchEvent(new Event('input', { bubbles: true }));
                            setTimeout(() => {
                                showYouTubePreview('edit', videoData.videoId);
                            }, 200);
                        }
                    } else if (retryCount < maxRetries) {
                        console.log('YouTube input not ready, retrying...', retryCount + 1);
                        setTimeout(() => setVideoId(retryCount + 1), 200);
                    } else {
                        console.error('Failed to set YouTube video ID after', maxRetries, 'attempts');
                        if (videoIdInput) {
                            videoIdInput.value = videoData.videoId || '';
                            console.log('Force set video ID:', videoData.videoId);
                        }
                    }
                } else if (videoData.videoHost === 'bunny') {
                    const bunnyVideoIdInput = document.getElementById('edit_topic_bunny_video_id');
                    const bunnyInputContainer = document.getElementById('edit_topic_bunny_input');

                    const isVisible = bunnyInputContainer &&
                        window.getComputedStyle(bunnyInputContainer).display !== 'none';

                    if (bunnyVideoIdInput && isVisible) {
                        bunnyVideoIdInput.value = videoData.videoId || '';
                        console.log('✓ Set Bunny video ID:', videoData.videoId);
                    } else if (retryCount < maxRetries) {
                        setTimeout(() => setVideoId(retryCount + 1), 200);
                    } else if (bunnyVideoIdInput) {
                        bunnyVideoIdInput.value = videoData.videoId || '';
                        console.log('Force set Bunny video ID:', videoData.videoId);
                    }
                } else {
                    console.log('No video host selected or video host is:', videoData.videoHost);
                }
            };

            // Start setting video ID after a short delay
            setTimeout(() => {
                setVideoId();
            }, 300);
        }, { once: true });

        modal.show();
    } catch (error) {
        console.error('Error in editTopic:', error);
        alert('An error occurred while opening the edit form. Please check the console for details.');
    }
}

function extractYouTubeId(input) {
    if (!input || input.trim() === '') {
        return null;
    }

    const trimmed = input.trim();

    // If it's already just an ID (11 characters, alphanumeric, dashes, underscores)
    if (/^[a-zA-Z0-9_-]{11}$/.test(trimmed)) {
        return trimmed;
    }

    // Try to extract from various YouTube URL formats
    const patterns = [
        /youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/,
        /youtu\.be\/([a-zA-Z0-9_-]{11})/,
        /youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/,
        /youtube\.com\/v\/([a-zA-Z0-9_-]{11})/,
        /youtube\.com\/.*[?&]v=([a-zA-Z0-9_-]{11})/,
    ];

    for (const pattern of patterns) {
        const match = trimmed.match(pattern);
        if (match && match[1]) {
            return match[1];
        }
    }

    return null;
}

function showYouTubePreview(mode, inputValue, retryCount = 0) {
    try {
        const prefix = mode === 'add' ? 'add_topic' : 'edit_topic';
        const maxRetries = 10; // Maximum number of retries

        // Try to get elements
        let previewContainer = document.getElementById(prefix + '_youtube_preview');
        let previewIframe = document.getElementById(prefix + '_youtube_preview_iframe');
        let youtubeInput = document.getElementById(prefix + '_youtube_input');

        // If elements not found and we haven't exceeded max retries, wait and retry
        if ((!previewContainer || !previewIframe) && retryCount < maxRetries) {
            console.log('Elements not found yet, waiting... (attempt ' + (retryCount + 1) + '/' + maxRetries + ')', { prefix });
            setTimeout(() => {
                showYouTubePreview(mode, inputValue, retryCount + 1);
            }, 200);
            return;
        }

        // If still not found after retries, log error and return
        if (!previewContainer || !previewIframe) {
            console.error('Preview elements not found after ' + maxRetries + ' attempts:', {
                previewContainer: prefix + '_youtube_preview',
                previewIframe: prefix + '_youtube_preview_iframe',
                mode: mode
            });
            return;
        }

        console.log('showYouTubePreview called:', { mode, inputValue, prefix, retryCount });
        console.log('Elements found:', {
            previewContainer: !!previewContainer,
            previewIframe: !!previewIframe,
            youtubeInput: !!youtubeInput,
            youtubeInputDisplay: youtubeInput ? window.getComputedStyle(youtubeInput).display : 'N/A'
        });

        // Check if parent container is visible
        if (youtubeInput) {
            const isVisible = window.getComputedStyle(youtubeInput).display !== 'none';
            if (!isVisible) {
                console.log('Parent YouTube input container is hidden, waiting...');
                // If parent is hidden, wait a bit more and retry
                if (retryCount < maxRetries) {
                    setTimeout(() => {
                        showYouTubePreview(mode, inputValue, retryCount + 1);
                    }, 200);
                    return;
                }
                if (previewContainer) {
                    previewContainer.style.display = 'none';
                }
                return;
            }
        }

        const videoId = extractYouTubeId(inputValue);
        console.log('Extracted video ID:', videoId);

        if (videoId && videoId.length === 11) {
            // Valid YouTube video ID found
            const embedUrl = `https://www.youtube.com/embed/${videoId}`;
            console.log('Setting embed URL:', embedUrl);
            previewIframe.src = embedUrl;
            previewContainer.style.display = 'block';
            console.log('Preview container display set to block');
        } else {
            // Invalid or empty - hide preview
            console.log('Invalid or empty video ID, hiding preview');
            previewIframe.src = '';
            previewContainer.style.display = 'none';
        }
    } catch (error) {
        console.error('Error in showYouTubePreview:', error);
    }
}

function toggleVideoInputs(mode) {
    const prefix = mode === 'add' ? 'add_topic' : 'edit_topic';
    const videoHostEl = document.getElementById(prefix + '_video_host');
    if (!videoHostEl) return;
    const videoHost = videoHostEl.value;

    // Container divs
    const youtubeInput = document.getElementById(prefix + '_youtube_input');
    const bunnyInput = document.getElementById(prefix + '_bunny_input');
    const uploadInput = document.getElementById(prefix + '_upload_input');
    const youtubePreview = document.getElementById(prefix + '_youtube_preview');
    // Actual inputs with name="video_id" (only the active one should be submitted)
    const youtubeIdInput = document.getElementById(prefix + '_video_id');
    const bunnyIdInput = document.getElementById(prefix + '_bunny_video_id');

    // Hide all video input containers
    if (youtubeInput) youtubeInput.style.display = 'none';
    if (bunnyInput) bunnyInput.style.display = 'none';
    if (uploadInput) uploadInput.style.display = 'none';
    if (youtubePreview) youtubePreview.style.display = 'none';

    // Disable inactive video_id inputs so only one is submitted (avoids duplicate name="video_id")
    if (youtubeIdInput) youtubeIdInput.disabled = (videoHost !== 'youtube');
    if (bunnyIdInput) bunnyIdInput.disabled = (videoHost !== 'bunny');

    // Show relevant input
    if (videoHost === 'youtube' && youtubeInput) {
        youtubeInput.style.display = 'block';
        if (youtubeIdInput && youtubeIdInput.value) {
            setTimeout(() => showYouTubePreview(mode, youtubeIdInput.value), 200);
        }
    } else if (videoHost === 'bunny' && bunnyInput) {
        bunnyInput.style.display = 'block';
    } else if (videoHost === 'upload' && uploadInput) {
        uploadInput.style.display = 'block';
    }
}

// Make functions globally accessible
window.showYouTubePreview = showYouTubePreview;
window.extractYouTubeId = extractYouTubeId;
window.toggleVideoInputs = toggleVideoInputs;

document.addEventListener('DOMContentLoaded', function() {
    // Use event delegation for YouTube preview inputs (since modals are loaded dynamically)
    document.addEventListener('input', function(e) {
        if (e.target && (e.target.id === 'add_topic_video_id' || e.target.id === 'edit_topic_video_id')) {
            const mode = e.target.id.startsWith('add_') ? 'add' : 'edit';
            showYouTubePreview(mode, e.target.value);
        }
    });

    // Handle edit chapter buttons with data attributes - use event delegation
    document.addEventListener('click', function(e) {
        const chapterButton = e.target.closest('.edit-chapter-btn');
        if (chapterButton) {
            e.preventDefault();
            e.stopPropagation();
            const chapterId = chapterButton.getAttribute('data-chapter-id');
            const title = chapterButton.getAttribute('data-chapter-title');
            const description = chapterButton.getAttribute('data-chapter-description');
            const isFree = chapterButton.getAttribute('data-chapter-is-free') === '1';
            const order = parseInt(chapterButton.getAttribute('data-chapter-order')) || 0;

            console.log('Edit chapter button clicked:', {chapterId, title, description, isFree, order});

            if (chapterId) {
                editChapter(chapterId, title, description, isFree, order);
            } else {
                console.error('Chapter ID not found on button');
            }
            return;
        }

        // Handle edit lesson buttons with data attributes
        const lessonButton = e.target.closest('.edit-lesson-btn');
        if (lessonButton) {
            e.preventDefault();
            e.stopPropagation();
            const lessonId = lessonButton.getAttribute('data-lesson-id');
            const title = lessonButton.getAttribute('data-lesson-title');
            const description = lessonButton.getAttribute('data-lesson-description');
            const isFree = lessonButton.getAttribute('data-lesson-is-free') === '1';
            const order = parseInt(lessonButton.getAttribute('data-lesson-order')) || 0;
            const status = lessonButton.getAttribute('data-lesson-status') || 'draft';
            const chapterId = lessonButton.getAttribute('data-chapter-id');

            console.log('Edit lesson button clicked:', {lessonId, title, description, isFree, order, status, chapterId});

            if (lessonId && chapterId) {
                editLesson(lessonId, title, description, isFree, order, status, chapterId);
            } else {
                console.error('Lesson ID or Chapter ID not found on button');
            }
            return;
        }

        // Handle edit topic buttons with data attributes
        const topicButton = e.target.closest('.edit-topic-btn');
        if (topicButton) {
            e.preventDefault();
            e.stopPropagation();
            const topicId = topicButton.getAttribute('data-topic-id');
            const title = topicButton.getAttribute('data-topic-title');
            const description = topicButton.getAttribute('data-topic-description');
            const isFree = topicButton.getAttribute('data-topic-is-free') === '1';
            const order = parseInt(topicButton.getAttribute('data-topic-order')) || 0;
            const type = topicButton.getAttribute('data-topic-type') || '';
            const videoHost = topicButton.getAttribute('data-topic-video-host') || '';
            const videoId = topicButton.getAttribute('data-topic-video-id') || '';
            const lessonId = topicButton.getAttribute('data-lesson-id');
            const chapterId = topicButton.getAttribute('data-chapter-id');

            console.log('=== Edit Topic Button Clicked ===');
            console.log('Topic ID:', topicId);
            console.log('Video Host:', videoHost);
            console.log('Video ID from data attribute:', videoId);
            console.log('Video ID length:', videoId ? videoId.length : 0);
            console.log('Video ID type:', typeof videoId);
            console.log('All data:', {
                topicId,
                title,
                description,
                isFree,
                order,
                type,
                videoHost,
                videoId,
                lessonId,
                chapterId
            });

            // Check if video_id is actually in the HTML attribute
            const rawVideoId = topicButton.getAttribute('data-topic-video-id');
            console.log('Raw video ID from attribute:', rawVideoId);
            console.log('Is video ID empty?', !videoId || videoId === '');

            if (topicId && lessonId && chapterId) {
                editTopic(topicId, title, description, isFree, order, type, videoHost, videoId, lessonId, chapterId);
            } else {
                console.error('Topic ID, Lesson ID or Chapter ID not found on button');
            }
            return;
        }
    });
});
</script>
@endpush
@endsection
