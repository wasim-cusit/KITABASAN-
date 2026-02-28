@extends('layouts.student')

@section('title', $course->title)
@section('page-title', $course->title)

@section('content')
<div class="container mx-auto px-4 lg:px-6">
    <!-- Header Section -->
    <div class="mb-4 sm:mb-6">
        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-2 break-words">{{ $course->title }}</h1>
        <div class="flex flex-wrap items-center gap-2 text-xs sm:text-sm text-gray-600 mb-3">
            <span class="flex items-center">
                <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                {{ $course->subject->grade->name ?? 'N/A' }}
            </span>
            <span class="hidden sm:inline">→</span>
            <span>{{ $course->subject->name ?? 'N/A' }}</span>
            @if($course->teacher)
                <span class="hidden sm:inline mx-1">•</span>
                <span class="text-xs sm:text-sm">By {{ $course->teacher->name }}</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Course Details -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Cover Image -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                @if($course->hasValidCoverImage())
                    <img src="{{ $course->getCoverImageUrl() }}" alt="{{ $course->title }}" class="w-full h-48 sm:h-64 lg:h-80 object-cover" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="hidden w-full h-48 sm:h-64 lg:h-80 flex items-center justify-center bg-gradient-to-br from-blue-400 to-indigo-600 text-white text-5xl font-bold">{{ $course->getTitleInitial() }}</div>
                @else
                    <div class="w-full h-48 sm:h-64 lg:h-80 flex items-center justify-center bg-gradient-to-br from-blue-400 to-indigo-600 text-white text-5xl font-bold">{{ $course->getTitleInitial() }}</div>
                @endif
            </div>

            <!-- Course Overview Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
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
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Published
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
                            <p class="text-xs sm:text-sm font-bold text-gray-900">
                                {{ $course->chapters->sum(function ($chapter) { return $chapter->lessons->count(); }) ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if($course->description)
                <div class="mb-4">
                    <h3 class="text-xs font-semibold text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                        </svg>
                        Description
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-700 line-clamp-3 break-words">{{ $course->description }}</p>
                </div>
                @endif

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

            <!-- Course Content Preview -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h2 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="break-words">Course Content</span>
                </h2>

                @if($course->chapters->count() > 0)
                    <div class="space-y-3 sm:space-y-4">
                        @foreach($course->chapters as $chapter)
                            <div class="border border-gray-200 rounded-lg p-3 sm:p-4 bg-gray-50">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-2 flex-1 min-w-0">
                                        <div class="flex items-center justify-center w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-blue-600 text-white font-bold text-xs sm:text-sm shrink-0">
                                            {{ $loop->iteration }}
                                        </div>
                                        <h3 class="text-sm sm:text-base font-semibold text-gray-900 break-words flex-1 min-w-0">{{ $chapter->title }}</h3>
                                    </div>
                                    <div class="flex gap-2 shrink-0 ml-8 sm:ml-0">
                                        @if($chapter->is_free)
                                            <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full font-medium shrink-0">FREE</span>
                                        @elseif(!$enrollment && !$course->is_free)
                                            <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full font-medium shrink-0">PAID</span>
                                        @endif
                                    </div>
                                </div>
                                @if($chapter->lessons->count() > 0)
                                    <div class="space-y-1.5 mt-2 pl-4 sm:pl-6">
                                        @foreach($chapter->lessons as $lesson)
                                            <div class="flex flex-wrap items-center gap-2 text-xs sm:text-sm">
                                                <span class="text-gray-400 shrink-0">•</span>
                                                <span class="text-gray-700 break-words flex-1 min-w-0">{{ $lesson->title }}</span>
                                                @if($lesson->is_free)
                                                    <span class="text-xs text-green-600 font-medium shrink-0">(Free)</span>
                                                @elseif(!$enrollment && !$course->is_free)
                                                    <span class="text-xs text-yellow-600 font-medium shrink-0">(Paid)</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">Course content will be available soon.</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 lg:sticky lg:top-24">
                <div class="text-center mb-4 sm:mb-6">
                    @if($course->is_free)
                        <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-green-600 mb-2">Free</div>
                    @else
                        <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-blue-600 mb-2">Rs. {{ number_format($course->price, 0) }}</div>
                    @endif
                </div>

                @if($enrollment)
                    <a href="{{ route('student.learning.index', $course->id) }}"
                       class="block w-full bg-blue-600 text-white text-center px-4 py-2.5 sm:py-3 hover:bg-blue-700 font-semibold mb-3 text-sm sm:text-base transition-colors">
                        Continue Learning
                    </a>
                    <p class="text-xs text-gray-500 text-center">
                        Enrolled on {{ $enrollment->enrolled_at->format('M d, Y') }}
                    </p>
                @else
                    @if($course->is_free)
                        <a href="{{ route('student.learning.index', $course->id) }}"
                           class="block w-full bg-green-600 text-white text-center px-4 py-2.5 sm:py-3 rounded-lg hover:bg-green-700 font-semibold mb-3 text-sm sm:text-base transition-colors">
                            Start Free Course
                        </a>
                    @else
                        @if(($freeChapters + $freeLessons + $previewChapters + $previewLessons) > 0)
                            <a href="{{ route('student.learning.index', $course->id) }}"
                               class="block w-full bg-green-600 text-white text-center px-4 py-2.5 sm:py-3 rounded-lg hover:bg-green-700 font-semibold mb-3 text-sm sm:text-base transition-colors">
                                View Free Content
                            </a>
                        @endif
                        <form action="{{ route('student.courses.enroll', $course->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2.5 sm:py-3 rounded-lg hover:bg-blue-700 font-semibold mb-3 text-sm sm:text-base transition-colors">
                                Purchase Course
                            </button>
                        </form>
                    @endif

                    @if(($freeChapters + $freeLessons + $previewChapters + $previewLessons) > 0)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-3">
                            <p class="text-xs sm:text-sm text-green-800 font-semibold mb-1">Free Preview Available!</p>
                            <p class="text-xs text-green-700">
                                {{ $freeChapters + $previewChapters }} free chapters and {{ $freeLessons + $previewLessons }} free lessons available.
                            </p>
                        </div>
                    @endif
                @endif

                <div class="border-t pt-4 mt-4">
                    <div class="space-y-2.5 text-xs sm:text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Chapters:</span>
                            <span class="font-semibold text-gray-900">{{ $course->chapters->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Lessons:</span>
                            <span class="font-semibold text-gray-900">{{ $totalLessons ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Duration:</span>
                            <span class="font-semibold text-gray-900">{{ $course->access_duration_months ?? $course->duration_months ?? 'N/A' }} months</span>
                        </div>
                        @if($course->difficulty_level)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Difficulty:</span>
                            <span class="font-semibold text-gray-900 capitalize">{{ $course->difficulty_level }}</span>
                        </div>
                        @endif
                        @if($course->language)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Language:</span>
                            <span class="font-semibold text-gray-900">
                                @php
                                    $languages = [
                                        'en' => 'English',
                                        'ur' => 'Urdu',
                                        'ar' => 'Arabic',
                                        'other' => 'Other'
                                    ];
                                    echo $languages[$course->language] ?? ucfirst($course->language);
                                @endphp
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

