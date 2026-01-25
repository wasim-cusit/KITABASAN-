@extends('layouts.student')

@section('title', 'Learning Dashboard')
@section('page-title', $book->title)

@push('styles')
<style>
    .collapse-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }
    .collapse-content.show {
        max-height: 5000px;
        transition: max-height 0.5s ease-in;
    }
    .collapse-icon {
        transition: transform 0.3s ease;
    }
    .collapse-icon.rotated {
        transform: rotate(90deg);
    }
    .video-container {
        position: relative;
        padding-bottom: 56.25%;
        height: 0;
        overflow: hidden;
        background: #000;
    }
    .video-container iframe,
    .video-container video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
    }
    .locked-content {
        position: relative;
        filter: blur(3px);
        pointer-events: none;
    }
    .lock-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    <div class="mb-6">
        <h1 class="text-xl lg:text-3xl font-bold">{{ $book->title }}</h1>
        <p class="text-sm lg:text-base text-gray-600 mt-2">{{ $book->description }}</p>

        @if(!$enrollment && !$book->is_free)
            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3 lg:p-4">
                <p class="text-yellow-800 text-xs lg:text-sm">
                    <strong>Note:</strong> You are viewing free content.
                    <a href="{{ route('student.courses.show', $book->id) }}" class="underline font-semibold">Purchase the course</a>
                    to access all lessons and topics.
                </p>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Course Content -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow p-4 lg:p-6">
                <h2 class="text-lg lg:text-xl font-bold mb-4">Course Content</h2>

                <div class="space-y-4">
                    @foreach($chapters as $chapterIndex => $chapter)
                        @php
                            $chapterCanAccess = $book->is_free || $enrollment || $chapter->is_free || $chapter->is_preview;
                        @endphp
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <!-- Chapter Header -->
                            <button onclick="toggleChapter({{ $chapter->id }})"
                                    class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition-colors text-left">
                                <div class="flex items-center gap-3 flex-1">
                                    <svg id="chapter-icon-{{ $chapter->id }}" class="w-5 h-5 text-gray-600 collapse-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <h3 class="text-base lg:text-lg font-semibold text-gray-900">{{ $chapter->title }}</h3>
                                        @if($chapter->description)
                                            <p class="text-xs text-gray-600 mt-1 line-clamp-1">{{ $chapter->description }}</p>
                                        @endif
                                    </div>
                                @if($chapter->is_free)
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">FREE</span>
                                @elseif(!$enrollment && !$book->is_free)
                                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">PAID</span>
                                @endif
                            </div>
                            </button>

                            <!-- Chapter Content (Lessons) -->
                            <div id="chapter-{{ $chapter->id }}" class="collapse-content">
                                <div class="p-4 space-y-3">
                                    @forelse($chapter->lessons as $lessonIndex => $lesson)
                                    @php
                                        $progress = Auth::user()->lessonProgress()
                                            ->where('lesson_id', $lesson->id)
                                            ->first();
                                        $isCompleted = $progress && $progress->is_completed;
                                        $watchPercentage = $progress ? $progress->watch_percentage : 0;
                                            $lessonCanAccess = $book->is_free
                                                || $enrollment
                                                || $lesson->is_free
                                                || $lesson->is_preview;

                                            // Check if lesson has video
                                            $hasLessonVideo = ($lesson->video_host === 'youtube' && !empty($lesson->video_id))
                                                           || ($lesson->video_host === 'bunny' && !empty($lesson->video_id))
                                                           || ($lesson->video_host === 'upload' && !empty($lesson->video_file));
                                    @endphp

                                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                                            <!-- Lesson Header -->
                                            <button onclick="toggleLesson({{ $chapter->id }}, {{ $lesson->id }})"
                                                    class="w-full flex items-center justify-between p-3 bg-white hover:bg-gray-50 transition-colors text-left">
                                                <div class="flex items-center gap-3 flex-1">
                                                    <svg id="lesson-icon-{{ $chapter->id }}-{{ $lesson->id }}" class="w-4 h-4 text-gray-600 collapse-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                            <div class="flex-1">
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-medium text-gray-900">{{ $lesson->title }}</span>
                                                    @if($isCompleted)
                                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                </svg>
                                                    @endif
                                                    @if($lesson->is_free)
                                                                <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">FREE</span>
                                                    @elseif(!$enrollment && !$book->is_free)
                                                                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">PAID</span>
                                                    @endif
                                                </div>
                                                        @if($lesson->description)
                                                            <p class="text-xs text-gray-600 mt-1 line-clamp-1">{{ $lesson->description }}</p>
                                                        @endif
                                                        @if($watchPercentage > 0 && !$isCompleted && $lessonCanAccess)
                                                    <div class="mt-1">
                                                        <div class="w-full bg-gray-200 rounded-full h-1">
                                                            <div class="bg-blue-600 h-1 rounded-full" style="width: {{ $watchPercentage }}%"></div>
                                                        </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0 ml-2">
                                                    @if($lessonCanAccess)
                                                        <a href="{{ route('student.learning.lesson', ['bookId' => $book->id, 'lessonId' => $lesson->id]) }}"
                                                           class="text-xs text-blue-600 hover:text-blue-700 font-medium whitespace-nowrap">
                                                            View Full Lesson →
                                                        </a>
                                                    @else
                                                        <a href="{{ route('student.courses.show', $book->id) }}"
                                                           class="text-xs text-gray-400 hover:text-gray-600 font-medium whitespace-nowrap underline">
                                                            Unlock
                                                        </a>
                                                    @endif
                                                </div>
                                            </button>

                                            <!-- Lesson Content (Video + Topics) -->
                                            <div id="lesson-{{ $chapter->id }}-{{ $lesson->id }}" class="collapse-content">
                                                <div class="p-4 space-y-4 bg-gray-50">
                                                    <!-- Lesson Video -->
                                                    @if($hasLessonVideo)
                                                        <div>
                                                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Lesson Video</h4>
                                                            <div class="video-container rounded-lg overflow-hidden {{ !$lessonCanAccess ? 'locked-content' : '' }}">
                                                                @if($lessonCanAccess)
                                                                    @if($lesson->video_host === 'youtube' && $lesson->video_id)
                                                                        @php
                                                                            $cleanVideoId = app(\App\Services\VideoService::class)->extractYouTubeId($lesson->video_id);
                                                                            $embedUrl = app(\App\Services\VideoService::class)->getYouTubeEmbedUrl($cleanVideoId);
                                                                        @endphp
                                                                        @if(!empty($cleanVideoId) && strlen($cleanVideoId) >= 11)
                                                                            <iframe src="{{ $embedUrl }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                                        @endif
                                                                    @elseif($lesson->video_host === 'bunny' && $lesson->video_id)
                                                                        <video controls>
                                                                            <source src="{{ app(\App\Services\VideoService::class)->getBunnyStreamUrl($lesson->video_id, $book->bunny_library_id ?? '') }}" type="video/mp4">
                                                                        </video>
                                                                    @elseif($lesson->video_host === 'upload' && $lesson->video_file)
                                                                        <video controls>
                                                                            <source src="{{ asset('storage/' . $lesson->video_file) }}" type="{{ $lesson->video_mime_type ?? 'video/mp4' }}">
                                                                        </video>
                                                                    @endif
                                                                @else
                                                                    <div class="lock-overlay">
                                                                        <div class="text-center text-white">
                                                                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                                            </svg>
                                                                            <p class="font-semibold">Premium Content</p>
                                                                            <a href="{{ route('student.courses.show', $book->id) }}" class="text-sm underline mt-1">Purchase to unlock</a>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Lesson Description -->
                                                    @if($lesson->description)
                                                        <div>
                                                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Description</h4>
                                                            <p class="text-sm text-gray-700">{{ $lesson->description }}</p>
                                                        </div>
                                                    @endif

                                                    <!-- Topics -->
                                                    @if($lesson->topics && $lesson->topics->count() > 0)
                                                        <div>
                                                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Topics ({{ $lesson->topics->count() }})</h4>
                                                            <div class="space-y-3">
                                                                @foreach($lesson->topics as $topicIndex => $topic)
                                                                    @php
                                                                        $topicCanAccess = $book->is_free
                                                                            || $enrollment
                                                                            || $topic->is_free
                                                                            || $topic->is_preview;
                                                                        $hasTopicVideo = ($topic->video_host === 'youtube' && !empty($topic->video_id))
                                                                                       || ($topic->video_host === 'bunny' && !empty($topic->video_id))
                                                                                       || ($topic->video_host === 'upload' && !empty($topic->video_file));
                                                                    @endphp

                                                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                                                        <!-- Topic Header -->
                                                                        <button onclick="toggleTopic({{ $chapter->id }}, {{ $lesson->id }}, {{ $topic->id }})"
                                                                                class="w-full flex items-center justify-between p-3 bg-white hover:bg-gray-50 transition-colors text-left">
                                                                            <div class="flex items-center gap-2 flex-1">
                                                                                <svg id="topic-icon-{{ $chapter->id }}-{{ $lesson->id }}-{{ $topic->id }}" class="w-4 h-4 text-gray-600 collapse-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                                                </svg>
                                                                                <div class="flex-1">
                                                                                    <div class="flex items-center gap-2 flex-wrap">
                                                                                        <span class="text-sm font-medium text-gray-900">{{ $topic->title }}</span>
                                                                                        @if($topic->type)
                                                                                            <span class="text-xs bg-purple-100 text-purple-800 px-2 py-0.5 rounded">{{ strtoupper($topic->type) }}</span>
                                                                                        @endif
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
                                                                                    </div>
                                                                                    @if($topic->description)
                                                                                        <p class="text-xs text-gray-600 mt-1 line-clamp-1">{{ $topic->description }}</p>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </button>

                                                                        <!-- Topic Content (Video + Description) -->
                                                                        <div id="topic-{{ $chapter->id }}-{{ $lesson->id }}-{{ $topic->id }}" class="collapse-content">
                                                                            <div class="p-4 bg-white">
                                                                                @if($hasTopicVideo)
                                                                                    <div class="mb-3">
                                                                                        <div class="flex items-center justify-between mb-2">
                                                                                            <h5 class="text-xs font-semibold text-gray-900">Topic Video</h5>
                                                                                            @if(!$topicCanAccess)
                                                                                                <a href="{{ route('student.courses.show', $book->id) }}"
                                                                                                   class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-semibold bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                                                                                                    Unlock
                                                                                                </a>
                                                                                            @endif
                                                                                        </div>
                                                                                        <div class="video-container rounded-lg overflow-hidden {{ !$topicCanAccess ? 'locked-content' : '' }}">
                                                                                            @if($topicCanAccess)
                                                                                                @if($topic->video_host === 'youtube' && $topic->video_id)
                                                                                                    @php
                                                                                                        $cleanVideoId = app(\App\Services\VideoService::class)->extractYouTubeId($topic->video_id);
                                                                                                        $embedUrl = app(\App\Services\VideoService::class)->getYouTubeEmbedUrl($cleanVideoId);
                                                                                                    @endphp
                                                                                                    @if(!empty($cleanVideoId) && strlen($cleanVideoId) >= 11)
                                                                                                        <iframe src="{{ $embedUrl }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                                                                    @endif
                                                                                                @elseif($topic->video_host === 'bunny' && $topic->video_id)
                                                                                                    <video controls>
                                                                                                        <source src="{{ app(\App\Services\VideoService::class)->getBunnyStreamUrl($topic->video_id, $book->bunny_library_id ?? '') }}" type="video/mp4">
                                                                                                    </video>
                                                                                                @elseif($topic->video_host === 'upload' && $topic->video_file)
                                                                                                    <video controls>
                                                                                                        <source src="{{ asset('storage/' . $topic->video_file) }}" type="{{ $topic->video_mime_type ?? 'video/mp4' }}">
                                                                                                    </video>
                                                                                                @endif
                                                                                            @else
                                                                                                <div class="lock-overlay">
                                                                                                    <div class="text-center text-white">
                                                                                                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                                                                        </svg>
                                                                                                        <p class="font-semibold">Premium Content</p>
                                                                                                        <a href="{{ route('student.courses.show', $book->id) }}" class="text-sm underline mt-1">Purchase to unlock</a>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                @else
                                                                                    @if(!$topicCanAccess)
                                                                                        <div class="relative rounded-lg border border-gray-200 bg-gray-100 p-6">
                                                                                            <div class="flex flex-col items-center justify-center text-center text-gray-700">
                                                                                                <svg class="w-10 h-10 mb-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                                                                </svg>
                                                                                                <p class="font-semibold">Premium Content</p>
                                                                                                <a href="{{ route('student.courses.show', $book->id) }}" class="text-sm underline mt-1">Purchase to unlock</a>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                @endif
                                                                                @if($topic->description)
                                                                                    <div>
                                                                                        <h5 class="text-xs font-semibold text-gray-900 mb-1">Description</h5>
                                                                                        <p class="text-xs text-gray-700">{{ $topic->description }}</p>
                                                    </div>
                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="text-sm text-gray-500 italic">No topics available in this lesson</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                        <p class="text-sm text-gray-500 italic p-3">No lessons available in this chapter</p>
                                @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 order-first lg:order-last">
            <div class="bg-white rounded-lg shadow p-4 mb-4">
                <h3 class="font-bold mb-4 text-sm lg:text-base">Progress</h3>
                @if($enrollment)
                    <div class="mb-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $enrollment->progress_percentage ?? 0 }}%"></div>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ $enrollment->progress_percentage ?? 0 }}% Complete</p>
                    </div>
                    <p class="text-xs text-gray-500">Expires: {{ $enrollment->expires_at ? $enrollment->expires_at->format('M d, Y') : 'Never' }}</p>
                @elseif($book->is_free)
                    <p class="text-sm text-gray-600">Free Course</p>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                        <p class="text-sm text-yellow-800 mb-2">Not Enrolled</p>
                        <a href="{{ route('student.courses.show', $book->id) }}" class="text-xs text-yellow-800 underline font-semibold">
                            Purchase Course →
                        </a>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-bold mb-4">Course Info</h3>
                <div class="space-y-2 text-sm">
                    <p><span class="font-medium">Total Chapters:</span> {{ $chapters->count() }}</p>
                    <p><span class="font-medium">Total Lessons:</span> {{ $chapters->sum(fn($ch) => $ch->lessons->count()) }}</p>
                    <p><span class="font-medium">Total Topics:</span> {{ $chapters->sum(fn($ch) => $ch->lessons->sum(fn($l) => $l->topics->count())) }}</p>
                    @if($book->teacher)
                        <p><span class="font-medium">Teacher:</span> {{ $book->teacher->name }}</p>
                    @endif
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
            console.error('Error in student learning page:', error);
            return null;
        }
    }

    function toggleChapter(chapterId) {
        safeExecute(function() {
            const content = document.getElementById('chapter-' + chapterId);
            const icon = document.getElementById('chapter-icon-' + chapterId);

            if (content && icon) {
                if (content.classList.contains('show')) {
                    content.classList.remove('show');
                    icon.classList.remove('rotated');
                } else {
                    content.classList.add('show');
                    icon.classList.add('rotated');
                }
            }
        });
    }

    function toggleLesson(chapterId, lessonId) {
        safeExecute(function() {
            const content = document.getElementById('lesson-' + chapterId + '-' + lessonId);
            const icon = document.getElementById('lesson-icon-' + chapterId + '-' + lessonId);

            if (content && icon) {
                if (content.classList.contains('show')) {
                    content.classList.remove('show');
                    icon.classList.remove('rotated');
                } else {
                    content.classList.add('show');
                    icon.classList.add('rotated');
                }
            }
        });
    }

    function toggleTopic(chapterId, lessonId, topicId) {
        safeExecute(function() {
            const content = document.getElementById('topic-' + chapterId + '-' + lessonId + '-' + topicId);
            const icon = document.getElementById('topic-icon-' + chapterId + '-' + lessonId + '-' + topicId);

            if (content && icon) {
                if (content.classList.contains('show')) {
                    content.classList.remove('show');
                    icon.classList.remove('rotated');
                } else {
                    content.classList.add('show');
                    icon.classList.add('rotated');
                }
            }
        });
    }

    // Auto-expand first chapter on load
    function initPage() {
        safeExecute(function() {
            const firstChapter = document.querySelector('[id^="chapter-"]');
            if (firstChapter) {
                const chapterId = firstChapter.id.replace('chapter-', '');
                if (chapterId && !isNaN(chapterId)) {
                    toggleChapter(parseInt(chapterId));
                }
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPage);
    } else {
        initPage();
    }

    // Make functions globally available
    window.toggleChapter = toggleChapter;
    window.toggleLesson = toggleLesson;
    window.toggleTopic = toggleTopic;
})();
</script>
@endpush
@endsection
