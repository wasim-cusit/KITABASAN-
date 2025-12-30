@extends('layouts.student')

@section('title', $topic->title)
@section('page-title', $topic->title)

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    @if(!$enrollment && !$book->is_free && !$topic->is_free && !$lesson->is_free && !$lesson->chapter->is_free)
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
                       class="text-blue-600 hover:text-blue-700 text-xs lg:text-sm mb-2 inline-block">
                        ← Back to Lesson: {{ $lesson->title }}
                    </a>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <h1 class="text-lg lg:text-2xl font-bold">{{ $topic->title }}</h1>
                        <div class="flex gap-2">
                            @if($topic->is_free || $lesson->is_free || $lesson->chapter->is_free)
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">FREE</span>
                            @elseif(!$enrollment && !$book->is_free)
                                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">PAID</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-black rounded-lg overflow-hidden mb-4" style="aspect-ratio: 16/9;">
                    @php
                        $canAccess = $book->is_free || $enrollment || $topic->is_free || $lesson->is_free || $lesson->chapter->is_free;
                    @endphp
                    @if($canAccess)
                        @if($topic->video_host === 'youtube' && $topic->video_id)
                            <iframe
                                src="{{ $videoService->getYouTubeEmbedUrl($topic->video_id) }}?enablejsapi=1"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                class="w-full h-full">
                            </iframe>
                        @elseif($topic->video_host === 'bunny' && $topic->video_id)
                            <video controls class="w-full h-full">
                                <source src="{{ $videoService->getBunnyStreamUrl($topic->video_id, $book->bunny_library_id ?? '') }}" type="video/mp4">
                            </video>
                        @elseif($topic->video_host === 'upload' && $topic->video_file)
                            <video controls class="w-full h-full">
                                <source src="{{ \Storage::url($topic->video_file) }}" type="{{ $topic->video_mime_type }}">
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
                    <p class="text-gray-700 text-sm lg:text-base">{{ $topic->description }}</p>
                </div>
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
                                    <div class="mb-2">
                                        <a href="{{ route('student.learning.lesson', ['bookId' => $book->id, 'lessonId' => $chapLesson->id]) }}"
                                           class="block text-sm {{ $chapLesson->id === $lesson->id ? 'text-blue-600 font-semibold' : 'text-gray-600' }}">
                                            {{ $chapLesson->title }}
                                        </a>
                                        @if($chapLesson->id === $lesson->id && $chapLesson->topics)
                                            <div class="ml-4 mt-1 space-y-1">
                                                @foreach($chapLesson->topics as $top)
                                                    <a href="{{ route('student.learning.topic', ['bookId' => $book->id, 'lessonId' => $chapLesson->id, 'topicId' => $top->id]) }}"
                                                       class="block text-xs {{ $top->id === $topic->id ? 'text-blue-600 font-semibold' : 'text-gray-500' }}">
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

