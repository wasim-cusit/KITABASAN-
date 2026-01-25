@extends('layouts.student')

@section('title', $topic->title)
@section('page-title', $topic->title)

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    @if(!$enrollment && !$book->is_free && !$topic->is_free && !$topic->is_preview)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 lg:p-4 mb-6">
            <p class="text-yellow-800 text-xs lg:text-sm">
                <strong>Premium Content:</strong> This topic requires course purchase.
                <a href="{{ route('student.courses.show', $book->id) }}" class="underline font-semibold">Purchase now</a> to access.
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Video Player -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow p-4 lg:p-6">
                <div class="mb-4">
                    <a href="{{ route('student.learning.lesson', ['bookId' => $book->id, 'lessonId' => $lesson->id]) }}"
                       class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 text-xs lg:text-sm mb-2">
                        <span aria-hidden="true">←</span>
                        <span>Back to Lesson</span>
                        <span class="text-gray-500">•</span>
                        <span class="text-gray-600 truncate max-w-[12rem]">{{ $lesson->title }}</span>
                    </a>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <h1 class="text-lg lg:text-2xl font-bold">{{ $topic->title }}</h1>
                        <div class="flex gap-2">
                            @if($topic->is_free)
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">FREE</span>
                            @elseif($topic->is_preview)
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">PREVIEW</span>
                            @elseif(!$enrollment && !$book->is_free)
                                <a href="{{ route('student.courses.show', $book->id) }}"
                                   class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded hover:bg-yellow-200">
                                    Unlock
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-black rounded-lg overflow-hidden mb-4" style="position: relative; padding-bottom: 56.25%; height: 0;">
                    @php
                        $canAccess = $book->is_free || $enrollment || $topic->is_free || $topic->is_preview;
                    @endphp
                    @if($canAccess)
                        @if($topic->video_host === 'youtube' && $topic->video_id)
                            @php
                                $cleanVideoId = $videoService->extractYouTubeId($topic->video_id);
                                $embedUrl = $videoService->getYouTubeEmbedUrl($cleanVideoId);
                            @endphp
                            @if(!empty($cleanVideoId) && strlen($cleanVideoId) >= 11)
                                <iframe
                                    src="{{ $embedUrl }}?enablejsapi=1"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;">
                                </iframe>
                            @else
                                <div class="flex items-center justify-center text-white" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                                    <p>Invalid YouTube video ID</p>
                                </div>
                            @endif
                        @elseif($topic->video_host === 'bunny' && $topic->video_id)
                            <video controls controlsList="nodownload" oncontextmenu="return false;" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                                <source src="{{ $videoService->getBunnyStreamUrl($topic->video_id, $book->bunny_library_id ?? '') }}" type="video/mp4">
                            </video>
                        @elseif($topic->video_host === 'upload' && $topic->video_file)
                            <video controls controlsList="nodownload" oncontextmenu="return false;" preload="metadata" id="topicVideoPlayer" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                                <source src="{{ asset('storage/' . $topic->video_file) }}" type="{{ $topic->video_mime_type }}">
                                Your browser does not support the video tag.
                            </video>
                            <script>
                                // Prevent video download
                                document.addEventListener('DOMContentLoaded', function() {
                                    const video = document.getElementById('topicVideoPlayer');
                                    if (video) {
                                        // Disable right-click context menu
                                        video.addEventListener('contextmenu', function(e) {
                                            e.preventDefault();
                                            return false;
                                        });
                                        // Prevent keyboard shortcuts
                                        video.addEventListener('keydown', function(e) {
                                            if (e.key === 's' || (e.ctrlKey && e.key === 's')) {
                                                e.preventDefault();
                                                return false;
                                            }
                                        });
                                    }
                                });
                            </script>
                        @else
                            <div class="flex items-center justify-center text-white" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                                <p>No video available</p>
                            </div>
                        @endif
                    @else
                        <div class="flex items-center justify-center text-white bg-gray-800" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                            <div class="text-center">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <p class="text-lg lg:text-xl font-semibold mb-2">Premium Content</p>
                                <p class="text-gray-400 mb-4 text-sm lg:text-base">Purchase this course to access this topic</p>
                                <a href="{{ route('student.courses.show', $book->id) }}" class="inline-block bg-blue-600 text-white px-4 lg:px-6 py-2 rounded-lg hover:bg-blue-700 text-sm lg:text-base">
                                    Purchase Course
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mb-4">
                    <h2 class="text-base lg:text-lg font-semibold mb-2">Description</h2>
                    <p class="text-gray-700 text-sm lg:text-base">{{ $topic->description ?? 'No description available.' }}</p>
                </div>

                <!-- Navigation: Previous/Next Topic -->
                @if(isset($previousTopic) || isset($nextTopic))
                    <div class="mt-6 pt-6 border-t flex items-center justify-between">
                    @if(isset($previousTopic))
                        <a href="{{ route('student.learning.topic', ['bookId' => $book->id, 'lessonId' => $lesson->id, 'topicId' => $previousTopic->id]) }}"
                           class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                <span class="text-sm font-medium">Previous Topic</span>
                                <span class="text-xs text-gray-500 hidden sm:inline">{{ \Illuminate\Support\Str::limit($previousTopic->title, 30) }}</span>
                            </a>
                        @else
                            <div></div>
                        @endif

                    @if(isset($nextTopic))
                        <a href="{{ route('student.learning.topic', ['bookId' => $book->id, 'lessonId' => $lesson->id, 'topicId' => $nextTopic->id]) }}"
                           class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <span class="text-sm font-medium">Next Topic</span>
                                <span class="text-xs opacity-90 hidden sm:inline">{{ \Illuminate\Support\Str::limit($nextTopic->title, 30) }}</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @else
                            <div></div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 order-first lg:order-last">
            <div class="bg-white rounded-lg shadow p-3 lg:p-4">
                <h3 class="font-bold mb-4 text-sm lg:text-base">Course Content</h3>
                <div class="space-y-2">
                    @foreach($chapters as $chapter)
                        <div class="mb-4">
                            <h4 class="font-semibold text-sm mb-2">{{ $chapter->title }}</h4>
                            <div class="space-y-1 pl-4">
                                @foreach($chapter->lessons as $chapLesson)
                                    @php
                                        $lessonCanAccess = $book->is_free || $enrollment || $chapLesson->is_free || $chapLesson->is_preview;
                                        $lessonUrl = $lessonCanAccess
                                            ? route('student.learning.lesson', ['bookId' => $book->id, 'lessonId' => $chapLesson->id])
                                            : route('student.courses.show', $book->id);
                                    @endphp
                                    <div class="mb-2">
                                        <a href="{{ $lessonUrl }}"
                                           class="block text-sm {{ $chapLesson->id === $lesson->id ? 'text-blue-600 font-semibold' : ($lessonCanAccess ? 'text-gray-600' : 'text-gray-400') }}">
                                            {{ $chapLesson->title }}
                                        </a>
                                        @if($chapLesson->id === $lesson->id && $chapLesson->topics && $chapLesson->topics->count() > 0)
                                            <div class="ml-4 mt-1 space-y-1">
                                                @foreach($chapLesson->topics as $top)
                                                    @php
                                                        $topicCanAccess = $book->is_free || $enrollment || $top->is_free || $top->is_preview;
                                                        $topicUrl = $topicCanAccess
                                                            ? route('student.learning.topic', ['bookId' => $book->id, 'lessonId' => $chapLesson->id, 'topicId' => $top->id])
                                                            : route('student.courses.show', $book->id);
                                                    @endphp
                                                    <a href="{{ $topicUrl }}"
                                                       class="block text-xs {{ $top->id === $topic->id ? 'text-blue-600 font-semibold' : ($topicCanAccess ? 'text-gray-500' : 'text-gray-400') }} hover:text-blue-600 transition-colors">
                                                        • {{ $top->title }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

