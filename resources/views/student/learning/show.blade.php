@extends('layouts.student')

@section('title', $lesson->title)
@section('page-title', $lesson->title)

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-6">
        <!-- Header with Back Button and Progress -->
                <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('student.learning.index', $book->id) }}"
                       class="text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $book->title }}</h1>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $completedLessons ?? 0 }} of {{ $totalLessons ?? 0 }} lessons completed
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <div class="text-sm font-semibold text-gray-700">{{ $progressPercentage ?? 0 }}% Complete</div>
                        <div class="w-48 bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                 style="width: {{ $progressPercentage ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Main Content Area -->
            <div class="lg:col-span-3">
                <!-- Video Player -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                    <div class="bg-black" style="aspect-ratio: 16/9; position: relative; width: 100%; padding-bottom: 56.25%;">
                        @php
                            $canAccess = $book->is_free
                                || $enrollment
                                || $lesson->is_free
                                || $lesson->is_preview;
                            // Check if lesson has video
                            $hasLessonVideo = ($lesson->video_host === 'youtube' && !empty($lesson->video_id))
                                           || ($lesson->video_host === 'bunny' && !empty($lesson->video_id))
                                           || ($lesson->video_host === 'upload' && !empty($lesson->video_file));
                            // Get first topic with video if lesson doesn't have one
                            $videoTopic = null;
                            if (!$hasLessonVideo && $lesson->topics && $lesson->topics->count() > 0) {
                                $videoTopic = $lesson->topics->first(function($topic) {
                                    return ($topic->video_host === 'youtube' && !empty($topic->video_id))
                                        || ($topic->video_host === 'bunny' && !empty($topic->video_id))
                                        || ($topic->video_host === 'upload' && !empty($topic->video_file));
                                });
                            }
                        @endphp
                        @if($canAccess)
                            @if($hasLessonVideo)
                                {{-- Lesson has video --}}
                                @if($lesson->video_host === 'youtube' && $lesson->video_id)
                                    @php
                                        // Extract clean video ID (handles both URLs and IDs)
                                        $cleanVideoId = $videoService->extractYouTubeId($lesson->video_id);
                                        $embedUrl = $videoService->getYouTubeEmbedUrl($cleanVideoId);
                                    @endphp
                                    @if(!empty($cleanVideoId) && strlen($cleanVideoId) >= 11)
                                        <iframe
                                            id="youtube-player-{{ $lesson->id }}"
                                            src="{{ $embedUrl }}"
                                            frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen
                                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;">
                                        </iframe>
                                        <script>
                                            console.log('YouTube Video Debug:', {
                                                lessonId: {{ $lesson->id }},
                                                videoHost: '{{ $lesson->video_host }}',
                                                original: '{{ $lesson->video_id }}',
                                                extracted: '{{ $cleanVideoId }}',
                                                embedUrl: '{{ $embedUrl }}',
                                                canAccess: {{ $canAccess ? 'true' : 'false' }}
                                            });
                                        </script>
                                    @else
                                        <div class="flex items-center justify-center h-full text-white">
                                            <div class="text-center">
                                                <p class="text-gray-400">Invalid YouTube video ID</p>
                                                <p class="text-gray-500 text-sm mt-2">Original: {{ $lesson->video_id }}</p>
                                                <p class="text-gray-500 text-sm">Extracted: {{ $cleanVideoId }}</p>
                                                <p class="text-gray-500 text-sm mt-2">Please check the video ID in the lesson settings</p>
                                            </div>
                                        </div>
                                    @endif
                                @elseif($lesson->video_host === 'bunny' && $lesson->video_id)
                                    <video controls class="w-full h-full" id="videoPlayer">
                                        <source src="{{ $videoService->getBunnyStreamUrl($lesson->video_id, $book->bunny_library_id ?? '') }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @elseif($lesson->video_host === 'upload' && $lesson->video_file)
                                    <video controls class="w-full h-full" id="videoPlayer">
                                        <source src="{{ asset('storage/' . $lesson->video_file) }}" type="{{ $lesson->video_mime_type ?? 'video/mp4' }}">
                                        Your browser does not support the video tag.
                                    </video>
                                @endif
                            @elseif($videoTopic)
                                {{-- Use first topic video if lesson doesn't have one --}}
                                @if($videoTopic->video_host === 'youtube' && $videoTopic->video_id)
                                    @php
                                        // Extract clean video ID (handles both URLs and IDs)
                                        $cleanVideoId = $videoService->extractYouTubeId($videoTopic->video_id);
                                        $embedUrl = $videoService->getYouTubeEmbedUrl($cleanVideoId);
                                    @endphp
                                    @if(!empty($cleanVideoId) && strlen($cleanVideoId) >= 11)
                                        <iframe
                                            id="youtube-player-topic-{{ $videoTopic->id }}"
                                            src="{{ $embedUrl }}"
                                            frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen
                                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;">
                                        </iframe>
                                        <script>
                                            console.log('YouTube Topic Video Debug:', {
                                                topicId: {{ $videoTopic->id }},
                                                videoHost: '{{ $videoTopic->video_host }}',
                                                original: '{{ $videoTopic->video_id }}',
                                                extracted: '{{ $cleanVideoId }}',
                                                embedUrl: '{{ $embedUrl }}'
                                            });
                                        </script>
                                    @else
                                        <div class="flex items-center justify-center h-full text-white">
                                            <div class="text-center">
                                                <p class="text-gray-400">Invalid YouTube video ID: {{ $videoTopic->video_id }}</p>
                                                <p class="text-gray-500 text-sm mt-2">Please check the video ID in the topic settings</p>
                                            </div>
                                        </div>
                                    @endif
                                @elseif($videoTopic->video_host === 'bunny' && $videoTopic->video_id)
                                    <video controls class="w-full h-full" id="videoPlayer">
                                        <source src="{{ $videoService->getBunnyStreamUrl($videoTopic->video_id, $book->bunny_library_id ?? '') }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @elseif($videoTopic->video_host === 'upload' && $videoTopic->video_file)
                                    <video controls class="w-full h-full" id="videoPlayer">
                                        <source src="{{ asset('storage/' . $videoTopic->video_file) }}" type="{{ $videoTopic->video_mime_type ?? 'video/mp4' }}">
                                        Your browser does not support the video tag.
                                    </video>
                                @endif
                            @else
                                {{-- No video available --}}
                                <div class="flex items-center justify-center h-full text-white">
                                    <div class="text-center">
                                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-gray-400">No video available for this lesson</p>
                                        @if($lesson->topics && $lesson->topics->count() > 0)
                                            <p class="text-gray-500 text-sm mt-2">Check topics below for video content</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="flex items-center justify-center h-full text-white bg-gray-800">
                                <div class="text-center">
                                <div class="text-center">
                                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    <p class="text-xl font-semibold mb-2">Premium Content</p>
                                    <p class="text-gray-400 mb-4">Purchase this course to access this lesson</p>
                                    <a href="{{ route('student.courses.show', $book->id) }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                                        Purchase Course
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Lesson Details -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $lesson->title }}</h2>
                            <p class="text-gray-600">{{ $lesson->description ?? 'No description available.' }}</p>
                        </div>
                        @if(auth()->user()->hasRole('teacher') || auth()->user()->hasRole('admin'))
                            <a href="{{ route('teacher.courses.chapters.lessons.edit', ['bookId' => $book->id, 'chapterId' => $lesson->chapter_id, 'lessonId' => $lesson->id]) }}"
                               class="text-sm bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                                Instructor View
                            </a>
                        @endif
                    </div>

                    <!-- Topics in This Lesson -->
                    @if($lesson->topics && $lesson->topics->count() > 0)
                        <div class="mt-6 border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Topics in This Lesson</h3>
                            <div class="space-y-3">
                                @foreach($lesson->topics as $topic)
                                    @php
                                        $topicCanAccess = $book->is_free
                                            || $enrollment
                                            || $topic->is_free
                                            || $topic->is_preview;
                                    @endphp
                                    <div class="relative flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                                        @if(!$topicCanAccess)
                                            <div class="absolute inset-0 bg-white/70 flex items-center justify-center rounded-lg">
                                                <div class="text-center text-gray-700">
                                                    <svg class="w-8 h-8 mx-auto mb-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                    </svg>
                                                    <p class="text-xs font-semibold">Premium Topic</p>
                                                    <a href="{{ route('student.courses.show', $book->id) }}" class="text-xs underline">Purchase course</a>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="flex items-center gap-3 flex-1">
                                            <div class="flex-shrink-0">
                                                @if($topic->type)
                                                    <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">{{ strtoupper($topic->type) }}</span>
                                                @else
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-gray-900">{{ $topic->title }}</h4>
                                                @if($topic->description)
                                                    <p class="text-sm text-gray-600 mt-1 line-clamp-1">{{ $topic->description }}</p>
                                                @endif
                                                <div class="flex items-center gap-2 mt-2">
                                                    @if($topic->is_free)
                                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">FREE</span>
                                                    @elseif($topic->is_preview)
                                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">PREVIEW</span>
                                                    @elseif(!$enrollment && !$book->is_free)
                                                        <a href="{{ route('student.courses.show', $book->id) }}"
                                                           class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded hover:bg-yellow-200">
                                                            Unlock
                                                        </a>
                                                    @endif
                                                    @if(($topic->video_host === 'youtube' && !empty($topic->video_id)) ||
                                                        ($topic->video_host === 'bunny' && !empty($topic->video_id)) ||
                                                        ($topic->video_host === 'upload' && !empty($topic->video_file)))
                                                        <span class="text-xs text-gray-500 flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                                                            </svg>
                                                            Video Available
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @if($topicCanAccess)
                                            <a href="{{ route('student.learning.topic', ['bookId' => $book->id, 'lessonId' => $lesson->id, 'topicId' => $topic->id]) }}"
                                               class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium whitespace-nowrap">
                                                View Topic
                                            </a>
                                        @else
                                            <a href="{{ route('student.courses.show', $book->id) }}"
                                               class="ml-4 px-4 py-2 bg-gray-200 text-gray-600 rounded-lg text-sm font-medium whitespace-nowrap text-center hover:bg-gray-300">
                                                Unlock
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Navigation: Previous/Next Lesson -->
                    <div class="mt-6 pt-6 border-t flex items-center justify-between">
                        @if(isset($previousLesson))
                            <a href="{{ route('student.learning.lesson', ['bookId' => $book->id, 'lessonId' => $previousLesson->id]) }}"
                               class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                <span class="text-sm font-medium">Previous Lesson</span>
                                <span class="text-xs text-gray-500 hidden sm:inline">{{ \Illuminate\Support\Str::limit($previousLesson->title, 30) }}</span>
                            </a>
                        @else
                            <div></div>
                        @endif

                        @if(isset($nextLesson))
                            <a href="{{ route('student.learning.lesson', ['bookId' => $book->id, 'lessonId' => $nextLesson->id]) }}"
                               class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <span class="text-sm font-medium">Next Lesson</span>
                                <span class="text-xs opacity-90 hidden sm:inline">{{ \Illuminate\Support\Str::limit($nextLesson->title, 30) }}</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @else
                            <div></div>
                        @endif
                    </div>
                </div>

                <!-- About This Course -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">About this course</h3>
                    <p class="text-gray-700 mb-4">{{ $book->description ?? 'No description available.' }}</p>
                    <div class="flex flex-wrap gap-3">
                        @if($book->difficulty_level)
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm font-medium">
                                {{ ucfirst($book->difficulty_level) }} Friendly
                            </span>
                        @endif
                        @if($book->total_duration)
                            <span class="px-3 py-1 bg-green-50 text-green-700 rounded-full text-sm font-medium">
                                {{ round($book->total_duration / 60) }} Hours Content
                            </span>
                        @endif
                        @if($book->certificate_enabled)
                            <span class="px-3 py-1 bg-purple-50 text-purple-700 rounded-full text-sm font-medium">
                                Certificate Included
                            </span>
                        @endif
                        <span class="px-3 py-1 bg-orange-50 text-orange-700 rounded-full text-sm font-medium">
                            Lifetime Access
                        </span>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Course Content -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-4 sticky top-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900">Course Content</h3>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">
                        {{ $totalLessons ?? 0 }} lessons • {{ $completedLessons ?? 0 }} completed
                    </p>

                    <div class="space-y-2 max-h-[calc(100vh-300px)] overflow-y-auto">
                        @if(isset($modules) && $modules->count() > 0)
                            @foreach($modules as $module)
                                <div class="module-section">
                                    <button onclick="toggleModule({{ $module->id }})"
                                            class="w-full flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg transition-colors">
                                        <span class="font-semibold text-sm text-gray-900">{{ $module->title }}</span>
                                        <svg id="icon-{{ $module->id }}" class="w-4 h-4 text-gray-500 transition-transform duration-300 ease-in-out transform-gpu" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div id="module-{{ $module->id }}" class="module-content pl-4 mt-1 hidden">
                                        @foreach($module->chapters as $chapter)
                                            @foreach($chapter->lessons as $chapLesson)
                                                @php
                                                    $canAccess = $book->is_free || $enrollment || $chapLesson->is_free || $chapLesson->is_preview;
                                                    $isCompleted = isset($lessonProgresses[$chapLesson->id]) && $lessonProgresses[$chapLesson->id]->is_completed;
                                                    $isCurrent = $chapLesson->id === $lesson->id;
                                                    $lessonUrl = $canAccess
                                                        ? route('student.learning.lesson', ['bookId' => $book->id, 'lessonId' => $chapLesson->id])
                                                        : route('student.courses.show', $book->id);
                                                @endphp
                                                <a href="{{ $lessonUrl }}"
                                                   class="flex items-center gap-2 p-2 rounded-lg transition-colors {{ $isCurrent ? 'bg-blue-50 text-blue-700' : ($canAccess ? 'text-gray-700 hover:bg-gray-50' : 'text-gray-400') }}">
                                                    @if($isCompleted)
                                                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    @elseif($isCurrent)
                                                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    @else
                                                        <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex-shrink-0"></div>
                                                    @endif
                                                    <span class="text-sm flex-1 min-w-0">
                                                        <span class="block text-[11px] text-gray-500 truncate">{{ $chapter->title }}</span>
                                                        <span class="block font-medium truncate">{{ $chapLesson->title }}</span>
                                                    </span>
                                                    @if($chapLesson->duration)
                                                        <span class="text-xs text-gray-500 flex-shrink-0">({{ gmdate('i:s', $chapLesson->duration) }})</span>
                                                    @elseif(!$canAccess)
                                                        <span class="text-xs text-gray-400 flex-shrink-0">Locked</span>
                                                    @endif
                                                </a>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Show chapters directly if no modules -->
                            @foreach($chapters as $chapter)
                                @foreach($chapter->lessons as $chapLesson)
                                    @php
                                        $canAccess = $book->is_free || $enrollment || $chapLesson->is_free || $chapLesson->is_preview;
                                        $isCompleted = isset($lessonProgresses[$chapLesson->id]) && $lessonProgresses[$chapLesson->id]->is_completed;
                                        $isCurrent = $chapLesson->id === $lesson->id;
                                        $lessonUrl = $canAccess
                                            ? route('student.learning.lesson', ['bookId' => $book->id, 'lessonId' => $chapLesson->id])
                                            : route('student.courses.show', $book->id);
                                    @endphp
                                    <a href="{{ $lessonUrl }}"
                                       class="flex items-center gap-2 p-2 rounded-lg transition-colors {{ $isCurrent ? 'bg-blue-50 text-blue-700' : ($canAccess ? 'text-gray-700 hover:bg-gray-50' : 'text-gray-400') }}">
                                        @if($isCompleted)
                                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @elseif($isCurrent)
                                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @else
                                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex-shrink-0"></div>
                                        @endif
                                        <span class="text-sm flex-1 min-w-0">
                                            <span class="block text-[11px] text-gray-500 truncate">{{ $chapter->title }}</span>
                                            <span class="block font-medium truncate">{{ $chapLesson->title }}</span>
                                        </span>
                                        @if($chapLesson->duration)
                                            <span class="text-xs text-gray-500 flex-shrink-0">({{ gmdate('i:s', $chapLesson->duration) }})</span>
                                        @elseif(!$canAccess)
                                            <span class="text-xs text-gray-400 flex-shrink-0">Locked</span>
                                        @endif
                                    </a>
                                @endforeach
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    'use strict';

    // Error handling wrapper
    function safeExecute(fn) {
        try {
            return fn();
        } catch (error) {
            console.error('Error in lesson page:', error);
            return null;
        }
    }

    function toggleModule(moduleId) {
        safeExecute(function() {
            const content = document.getElementById('module-' + moduleId);
            const icon = document.getElementById('icon-' + moduleId);

            if (content && icon) {
                if (content.classList.contains('hidden')) {
                    content.classList.remove('hidden');
                    icon.style.transform = 'rotate(180deg)';
                } else {
                    content.classList.add('hidden');
                    icon.style.transform = 'rotate(0deg)';
                }
            }
        });
    }

    // Make function globally available
    window.toggleModule = toggleModule;

    // Expand module containing current lesson
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            safeExecute(function() {
                @if(isset($modules) && $modules->count() > 0)
                    @foreach($modules as $module)
                        @foreach($module->chapters as $chapter)
                            @foreach($chapter->lessons as $chapLesson)
                                @if($chapLesson->id === $lesson->id)
                                    // Expand this module
                                    const module{{ $module->id }} = document.getElementById('module-{{ $module->id }}');
                                    const icon{{ $module->id }} = document.getElementById('icon-{{ $module->id }}');
                                    if (module{{ $module->id }}) {
                                        module{{ $module->id }}.classList.remove('hidden');
                                        if (icon{{ $module->id }}) {
                                            icon{{ $module->id }}.style.transform = 'rotate(180deg)';
                                        }
                                    }
                                @endif
                            @endforeach
                        @endforeach
                    @endforeach
                @endif
            });
        });
    } else {
        safeExecute(function() {
            @if(isset($modules) && $modules->count() > 0)
                @foreach($modules as $module)
                    @foreach($module->chapters as $chapter)
                        @foreach($chapter->lessons as $chapLesson)
                            @if($chapLesson->id === $lesson->id)
                                // Expand this module
                                const module{{ $module->id }} = document.getElementById('module-{{ $module->id }}');
                                const icon{{ $module->id }} = document.getElementById('icon-{{ $module->id }}');
                                if (module{{ $module->id }}) {
                                    module{{ $module->id }}.classList.remove('hidden');
                                    if (icon{{ $module->id }}) {
                                        icon{{ $module->id }}.style.transform = 'rotate(180deg)';
                                    }
                                }
                            @endif
                        @endforeach
                    @endforeach
                @endforeach
            @endif
        });
    }

    // Video progress tracking
    function initVideoTracking() {
        safeExecute(function() {
            const videoPlayer = document.getElementById('videoPlayer');
            if (!videoPlayer) return;

            let lastUpdateTime = 0;

            videoPlayer.addEventListener('timeupdate', function() {
                safeExecute(function() {
                    if (!videoPlayer.duration || videoPlayer.duration === 0) return;

                    const currentTime = Math.floor(videoPlayer.currentTime);
                    const progress = (videoPlayer.currentTime / videoPlayer.duration) * 100;

                    // Update progress every 5 seconds
                    if (currentTime - lastUpdateTime >= 5) {
                        lastUpdateTime = currentTime;
                        fetch('{{ route("student.learning.progress") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                lesson_id: {{ $lesson->id }},
                                watch_percentage: Math.round(progress),
                                last_watched_position: currentTime
                            })
                        }).catch(function(err) {
                            console.error('Progress update failed:', err);
                        });
                    }
                });
            });

            // Mark as completed when video ends
            videoPlayer.addEventListener('ended', function() {
                safeExecute(function() {
                    fetch('{{ route("student.learning.progress") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            lesson_id: {{ $lesson->id }},
                            watch_percentage: 100,
                            is_completed: true
                        })
                    }).then(function() {
                        // Reload page to update completion status
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    }).catch(function(err) {
                        console.error('Completion update failed:', err);
                    });
                });
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initVideoTracking);
    } else {
        initVideoTracking();
    }
})();
</script>
@endpush
@endsection
