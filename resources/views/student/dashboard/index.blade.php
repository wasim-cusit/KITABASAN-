@extends('layouts.student')

@section('title', 'Student Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .course-card {
        transition: all 0.3s ease;
    }
    .course-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }
    .step-item {
        transition: all 0.2s ease;
    }
    .step-item:hover {
        background-color: #f9fafb;
    }
    .video-thumbnail {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
    }
    .play-button {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .play-button:hover {
        background: rgba(255, 255, 255, 1);
        transform: translate(-50%, -50%) scale(1.1);
    }
    .accordion-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }
    .accordion-content.open {
        max-height: 2000px;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-6 mb-6 lg:mb-8">
        <div class="bg-white rounded-lg shadow-sm p-4 lg:p-6 border border-gray-100">
            <h3 class="text-gray-500 text-xs lg:text-sm font-medium mb-1">Enrolled Courses</h3>
            <p class="text-xl lg:text-3xl font-bold text-gray-900">{{ $stats['enrolled_courses'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 lg:p-6 border border-gray-100">
            <h3 class="text-gray-500 text-xs lg:text-sm font-medium mb-1">Completed Courses</h3>
            <p class="text-xl lg:text-3xl font-bold text-green-600">{{ $stats['completed_courses'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 lg:p-6 border border-gray-100">
            <h3 class="text-gray-500 text-xs lg:text-sm font-medium mb-1">In Progress</h3>
            <p class="text-xl lg:text-3xl font-bold text-blue-600">{{ $stats['in_progress_courses'] }}</p>
        </div>
    </div>

    <!-- Course Progress Cards -->
    @if($enrollments->count() > 0)
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl lg:text-2xl font-bold text-gray-900">My Learning Journey</h2>
            <a href="{{ route('student.courses.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                Browse More Courses →
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            @foreach($enrollments as $enrollment)
                @php
                    $course = $enrollment->book;
                    $teacher = $course->teacher;
                    $teacherProfile = $teacher ? $teacher->teacherProfile : null;
                    $chapters = $course->chapters;
                    $totalLessons = $chapters->sum(fn($ch) => $ch->lessons->count());
                    $completedLessons = $enrollment->lessonProgress->where('is_completed', true)->count();
                    $progressPercentage = $enrollment->progress_percentage ?? 0;

                    // Get lesson progress map
                    $lessonProgressMap = $enrollment->lessonProgress ? $enrollment->lessonProgress->keyBy('lesson_id') : collect();

                    // Build steps from chapters and lessons
                    $steps = [];
                    $stepNumber = 1;
                    foreach ($chapters as $chapter) {
                        foreach ($chapter->lessons as $lesson) {
                            $lessonProgress = $lessonProgressMap->get($lesson->id);
                            $isCompleted = $lessonProgress && $lessonProgress->is_completed;

                            $steps[] = [
                                'number' => $stepNumber++,
                                'type' => 'lesson',
                                'title' => $lesson->title,
                                'description' => $lesson->description ?? 'Complete this lesson to continue',
                                'chapter' => $chapter->title,
                                'is_completed' => $isCompleted,
                                'lesson_id' => $lesson->id,
                                'chapter_id' => $chapter->id,
                                'has_video' => !empty($lesson->video_id) || !empty($lesson->video_file),
                            ];
                        }
                    }

                    // Separate completed and upcoming steps
                    $completedSteps = collect($steps)->where('is_completed', true);
                    $upcomingSteps = collect($steps)->where('is_completed', false);
                @endphp

                <div class="course-card bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <!-- Course Header -->
                    <div class="p-6 pb-4 border-b border-gray-100">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $course->title }}</h3>
                                <p class="text-sm text-gray-500">{{ $course->subject->name ?? 'Course' }}</p>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-700">Progress</span>
                                <span class="text-sm font-bold text-blue-600">{{ round($progressPercentage) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2.5 rounded-full transition-all duration-500"
                                     style="width: {{ $progressPercentage }}%"></div>
                            </div>
                        </div>

                        <!-- Teacher Profile -->
                        @if($teacher)
                            <div class="flex items-center space-x-3 pt-4 border-t border-gray-100">
                                @if($teacherProfile && $teacherProfile->profile_image)
                                    <img src="{{ \Storage::url($teacherProfile->profile_image) }}"
                                         alt="{{ $teacher->name }}"
                                         class="w-12 h-12 rounded-full object-cover border-2 border-blue-100">
                                @elseif($teacher->profile_image)
                                    <img src="{{ \Storage::url($teacher->profile_image) }}"
                                         alt="{{ $teacher->name }}"
                                         class="w-12 h-12 rounded-full object-cover border-2 border-blue-100">
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center border-2 border-blue-100">
                                        <span class="text-white font-semibold text-sm">{{ $teacher->getInitials() }}</span>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $teacher->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">
                                        @if($teacherProfile && $teacherProfile->specializations)
                                            {{ $teacherProfile->specializations }}
                                        @else
                                            Senior Instructor
                                        @endif
                                    </p>
                                    @if($teacherProfile && $teacherProfile->bio)
                                        <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ Str::limit($teacherProfile->bio, 40) }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Course Intro Video (if available) -->
                    @if($course->intro_video_url)
                        <div class="px-6 pt-4">
                            <div class="video-thumbnail bg-gray-100 aspect-video relative group cursor-pointer"
                                 onclick="window.location.href='{{ route('student.learning.index', $course->id) }}'">
                                @if($course->intro_video_provider === 'youtube')
                                    @php
                                        // Extract YouTube video ID
                                        $videoId = '';
                                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $course->intro_video_url, $matches)) {
                                            $videoId = $matches[1];
                                        }
                                    @endphp
                                    @if($videoId)
                                        <img src="https://img.youtube.com/vi/{{ $videoId }}/maxresdefault.jpg"
                                             alt="Course Introduction"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center">
                                            <span class="text-white font-semibold">Course Introduction</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center">
                                        <span class="text-white font-semibold">Course Introduction</span>
                                    </div>
                                @endif
                                <div class="play-button">
                                    <svg class="w-6 h-6 text-blue-600 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"></path>
                                    </svg>
                                </div>
                                <div class="absolute bottom-2 left-2 bg-black bg-opacity-60 text-white text-xs px-2 py-1 rounded">
                                    Welcome message from instructor
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Steps Checklist -->
                    <div class="p-6 pt-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-4">Learning Steps</h4>

                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            <!-- Completed Steps -->
                            @foreach($completedSteps as $step)
                                <div class="step-item flex items-start space-x-3 p-3 rounded-lg bg-green-50 border border-green-100">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">Step {{ $step['number'] }}</p>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-0.5">{{ $step['title'] }}</p>
                                        @if($step['has_video'])
                                            <span class="inline-flex items-center mt-1 text-xs text-gray-500">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                                                </svg>
                                                Video Lesson
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <!-- Upcoming Steps (Accordion) -->
                            @if($upcomingSteps->count() > 0)
                                <div x-data="{ open: false }" class="border-t border-gray-200 pt-2 mt-2">
                                    <button @click="open = !open"
                                            class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                        <span class="text-sm font-semibold text-gray-700">
                                            Upcoming Steps ({{ $upcomingSteps->count() }})
                                        </span>
                                        <svg class="w-5 h-5 text-gray-500 transition-transform"
                                             :class="{ 'rotate-180': open }"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <div class="accordion-content" :class="{ 'open': open }">
                                        <div class="space-y-2 mt-2">
                                            @foreach($upcomingSteps->take(5) as $step)
                                                <div class="step-item flex items-start space-x-3 p-3 rounded-lg border border-gray-200 hover:border-blue-200 hover:bg-blue-50 transition-colors">
                                                    <div class="flex-shrink-0 mt-0.5">
                                                        <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-xs font-semibold text-gray-600">
                                                            {{ $step['number'] }}
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900">{{ $step['title'] }}</p>
                                                        <p class="text-xs text-gray-500 mt-0.5">{{ $step['description'] }}</p>
                                                        @if($step['has_video'])
                                                            <span class="inline-flex items-center mt-1 text-xs text-gray-500">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                                                                </svg>
                                                                Video Lesson
                                                            </span>
                                                        @endif
                                                        <a href="{{ route('student.learning.lesson', ['bookId' => $course->id, 'lessonId' => $step['lesson_id']]) }}"
                                                           class="inline-flex items-center mt-2 text-xs font-medium text-blue-600 hover:text-blue-700">
                                                            Continue Learning →
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if($upcomingSteps->count() > 5)
                                                <p class="text-xs text-gray-500 text-center py-2">
                                                    + {{ $upcomingSteps->count() - 5 }} more steps
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="px-6 pb-6 pt-4 border-t border-gray-100">
                        <div class="flex flex-col sm:flex-row gap-2">
                            @if($progressPercentage < 100)
                                <a href="{{ route('student.learning.index', $course->id) }}"
                                   class="flex-1 bg-blue-600 text-white text-center px-4 py-2.5 rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm">
                                    Continue Learning
                                </a>
                            @else
                                <a href="{{ route('student.learning.index', $course->id) }}"
                                   class="flex-1 bg-green-600 text-white text-center px-4 py-2.5 rounded-lg hover:bg-green-700 transition-colors font-medium text-sm">
                                    Review Course
                                </a>
                            @endif
                            <a href="{{ route('student.courses.show', $course->id) }}"
                               class="flex-1 bg-gray-100 text-gray-700 text-center px-4 py-2.5 rounded-lg hover:bg-gray-200 transition-colors font-medium text-sm">
                                Preview Course
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="max-w-md mx-auto">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No courses enrolled yet</h3>
                <p class="text-gray-600 mb-6">Start your learning journey by enrolling in a course!</p>
                <a href="{{ route('student.courses.index') }}"
                   class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Browse Courses
                </a>
            </div>
        </div>
    @endif

    <!-- Recent Activity Section -->
    @if($recentProgress->count() > 0)
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg lg:text-xl font-bold mb-4 text-gray-900">Recent Activity</h2>
            <div class="space-y-3">
                @foreach($recentProgress as $progress)
                    <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors border border-gray-100">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm text-gray-900 truncate">{{ $progress->lesson->title }}</p>
                            <p class="text-xs text-gray-600 mt-0.5 truncate">{{ $progress->lesson->chapter->book->title }}</p>
                        </div>
                        <div class="flex-shrink-0 ml-4 text-right">
                            <p class="text-xs text-gray-500">{{ $progress->last_watched_at->diffForHumans() }}</p>
                            @if($progress->is_completed)
                                <span class="inline-flex items-center mt-1 text-xs text-green-600">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Completed
                                </span>
                            @endif
                        </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
