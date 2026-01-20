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
                    <a href="{{ route('student.courses.show', $book->id) }}" 
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
                            $canAccess = $book->is_free || $enrollment || $lesson->is_free || ($lesson->chapter && $lesson->chapter->is_free);
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

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3 mt-6">
                        @if($lesson->documents && $lesson->documents->count() > 0)
                            <button class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download Resources
                            </button>
                        @endif
                        <button class="flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            View Notes
                        </button>
                        <button class="flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            Ask Question
                        </button>
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
                                        <svg id="icon-{{ $module->id }}" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div id="module-{{ $module->id }}" class="module-content pl-4 mt-1 hidden">
                                        @foreach($module->chapters as $chapter)
                                            @foreach($chapter->lessons as $chapLesson)
                                                @php
                                                    $canAccess = $book->is_free || $enrollment || $chapLesson->is_free || ($chapter && $chapter->is_free);
                                                    $isCompleted = isset($lessonProgresses[$chapLesson->id]) && $lessonProgresses[$chapLesson->id]->is_completed;
                                                    $isCurrent = $chapLesson->id === $lesson->id;
                                                @endphp
                                                <a href="{{ route('student.learning.lesson', ['bookId' => $book->id, 'lessonId' => $chapLesson->id]) }}"
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
                                                    <span class="text-sm flex-1 truncate">{{ $chapLesson->title }}</span>
                                                    @if($chapLesson->duration)
                                                        <span class="text-xs text-gray-500 flex-shrink-0">({{ gmdate('i:s', $chapLesson->duration) }})</span>
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
                                        $canAccess = $book->is_free || $enrollment || $chapLesson->is_free || ($chapter && $chapter->is_free);
                                        $isCompleted = isset($lessonProgresses[$chapLesson->id]) && $lessonProgresses[$chapLesson->id]->is_completed;
                                        $isCurrent = $chapLesson->id === $lesson->id;
                                    @endphp
                                    <a href="{{ route('student.learning.lesson', ['bookId' => $book->id, 'lessonId' => $chapLesson->id]) }}"
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
                                        <span class="text-sm flex-1 truncate">{{ $chapLesson->title }}</span>
                                        @if($chapLesson->duration)
                                            <span class="text-xs text-gray-500 flex-shrink-0">({{ gmdate('i:s', $chapLesson->duration) }})</span>
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
function toggleModule(moduleId) {
    const content = document.getElementById('module-' + moduleId);
    const icon = document.getElementById('icon-' + moduleId);
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

// Expand module containing current lesson
document.addEventListener('DOMContentLoaded', function() {
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

// Video progress tracking
document.addEventListener('DOMContentLoaded', function() {
    const videoPlayer = document.getElementById('videoPlayer');
    if (videoPlayer) {
        let lastUpdateTime = 0;
        
        videoPlayer.addEventListener('timeupdate', function() {
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
                }).catch(err => console.error('Progress update failed:', err));
            }
        });

        // Mark as completed when video ends
        videoPlayer.addEventListener('ended', function() {
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
            }).then(() => {
                // Reload page to update completion status
                setTimeout(() => window.location.reload(), 1000);
            }).catch(err => console.error('Completion update failed:', err));
        });
    }
});
</script>
@endpush
@endsection
