@extends('layouts.student')

@section('title', 'Watch Lesson')
@section('page-title', $lesson->title)

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    @if(!$enrollment && !$book->is_free && !$lesson->is_free && !$lesson->chapter->is_free)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 lg:p-4 mb-6">
            <p class="text-yellow-800 text-xs lg:text-sm">
                <strong>Premium Content:</strong> This lesson requires course purchase.
                <a href="{{ route('student.courses.show', $book->id) }}" class="underline font-semibold">Purchase now</a> to access.
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Video Player -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow p-4 lg:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
                    <h1 class="text-lg lg:text-2xl font-bold">{{ $lesson->title }}</h1>
                    <div class="flex gap-2">
                        @if($lesson->is_free || $lesson->chapter->is_free)
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">FREE</span>
                        @elseif(!$enrollment && !$book->is_free)
                            <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">PAID</span>
                        @endif
                    </div>
                </div>

                <div class="bg-black rounded-lg overflow-hidden mb-4" style="aspect-ratio: 16/9;">
                    @php
                        $canAccess = $book->is_free || $enrollment || $lesson->is_free || $lesson->chapter->is_free;
                    @endphp
                    @if($canAccess)
                        @if($lesson->video_host === 'youtube' && $lesson->video_id)
                            <iframe
                                src="{{ $videoService->getYouTubeEmbedUrl($lesson->video_id) }}?enablejsapi=1"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                class="w-full h-full">
                            </iframe>
                        @elseif($lesson->video_host === 'bunny' && $lesson->video_id)
                            <video controls class="w-full h-full">
                                <source src="{{ $videoService->getBunnyStreamUrl($lesson->video_id, $book->bunny_library_id ?? '') }}" type="video/mp4">
                            </video>
                        @elseif($lesson->video_host === 'upload' && $lesson->video_file)
                            <video controls class="w-full h-full" id="videoPlayer">
                                <source src="{{ \Storage::url($lesson->video_file) }}" type="{{ $lesson->video_mime_type }}">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <div class="flex items-center justify-center h-full text-white">
                                <p>No video available</p>
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

                <div class="mb-4">
                    <h2 class="text-lg font-semibold mb-2">Description</h2>
                    <p class="text-gray-700">{{ $lesson->description }}</p>
                </div>

                <!-- Topics Section -->
                @if($lesson->topics->count() > 0)
                    <div class="mt-6">
                        <h2 class="text-lg font-semibold mb-4">Topics in this Lesson</h2>
                        <div class="space-y-2">
                            @foreach($lesson->topics as $topic)
                                @php
                                    $canAccessTopic = $book->is_free || $enrollment || $topic->is_free || $lesson->is_free || $lesson->chapter->is_free;
                                @endphp
                                <div class="border rounded-lg p-3 {{ $canAccessTopic ? 'hover:bg-gray-50' : 'bg-gray-50 opacity-60' }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            @if($canAccessTopic)
                                                <a href="{{ route('student.learning.topic', ['bookId' => $book->id, 'lessonId' => $lesson->id, 'topicId' => $topic->id]) }}"
                                                   class="text-gray-900 hover:text-blue-600">
                                                    {{ $topic->title }}
                                                </a>
                                            @else
                                                <span class="text-gray-400 cursor-not-allowed">{{ $topic->title }}</span>
                                            @endif
                                            @if($topic->is_free)
                                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">FREE</span>
                                            @elseif(!$enrollment && !$book->is_free)
                                                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">PAID</span>
                                            @endif
                                        </div>
                                        @if(!$canAccessTopic)
                                            <span class="text-xs text-yellow-600">
                                                <a href="{{ route('student.courses.show', $book->id) }}" class="underline">Purchase</a>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
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
                            <h4 class="font-semibold text-sm mb-2 flex items-center gap-2">
                                {{ $chapter->title }}
                                @if($chapter->is_free)
                                    <span class="text-xs bg-green-100 text-green-800 px-1 py-0.5 rounded">FREE</span>
                                @endif
                            </h4>
                            <div class="space-y-1 pl-4">
                                @foreach($chapter->lessons as $chapLesson)
                                    @php
                                        $canAccess = $book->is_free || $enrollment || $chapLesson->is_free || $chapter->is_free;
                                    @endphp
                                    <a href="{{ route('student.learning.lesson', ['bookId' => $book->id, 'lessonId' => $chapLesson->id]) }}"
                                       class="block text-sm {{ $chapLesson->id === $lesson->id ? 'text-blue-600 font-semibold' : ($canAccess ? 'text-gray-600' : 'text-gray-400') }}">
                                        {{ $chapLesson->title }}
                                        @if($chapLesson->is_free)
                                            <span class="text-xs text-green-600">(Free)</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoPlayer = document.getElementById('videoPlayer');
    if (videoPlayer) {
        // Track progress
        videoPlayer.addEventListener('timeupdate', function() {
            const progress = (videoPlayer.currentTime / videoPlayer.duration) * 100;

            // Update progress every 5 seconds
            if (Math.floor(videoPlayer.currentTime) % 5 === 0) {
                fetch('{{ route("student.learning.progress") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        lesson_id: {{ $lesson->id }},
                        watch_percentage: Math.round(progress),
                        last_watched_position: Math.floor(videoPlayer.currentTime)
                    })
                });
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
            });
        });
    }
});
</script>
@endpush
@endsection
