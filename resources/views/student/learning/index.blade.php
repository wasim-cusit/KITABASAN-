@extends('layouts.app')

@section('title', 'Learning Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">{{ $book->title }}</h1>
        <p class="text-gray-600 mt-2">{{ $book->description }}</p>

        @if(!$enrollment && !$book->is_free)
            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-yellow-800 text-sm">
                    <strong>Note:</strong> You are viewing free content.
                    <a href="{{ route('student.courses.show', $book->id) }}" class="underline font-semibold">Purchase the course</a>
                    to access all lessons and topics.
                </p>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Course Content -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Course Content</h2>

                <div class="space-y-6">
                    @foreach($chapters as $chapter)
                        <div class="border-b pb-4 last:border-b-0">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-semibold">{{ $chapter->title }}</h3>
                                @if($chapter->is_free)
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">FREE</span>
                                @elseif(!$enrollment && !$book->is_free)
                                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">PAID</span>
                                @endif
                            </div>
                            @if($chapter->description)
                                <p class="text-sm text-gray-600 mb-3">{{ $chapter->description }}</p>
                            @endif
                            <div class="space-y-2 pl-4">
                                @forelse($chapter->lessons as $lesson)
                                    @php
                                        $progress = Auth::user()->lessonProgress()
                                            ->where('lesson_id', $lesson->id)
                                            ->first();
                                        $isCompleted = $progress && $progress->is_completed;
                                        $watchPercentage = $progress ? $progress->watch_percentage : 0;
                                        $canAccess = $book->is_free || $enrollment || $lesson->is_free || $chapter->is_free;
                                    @endphp
                                    <div class="border rounded-lg p-3 hover:bg-gray-50 {{ $isCompleted ? 'border-green-200 bg-green-50' : '' }}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    @if($canAccess)
                                                        <a href="{{ route('student.learning.lesson', ['bookId' => $book->id, 'lessonId' => $lesson->id]) }}"
                                                           class="font-medium {{ $isCompleted ? 'text-green-700' : 'text-gray-900' }} hover:text-blue-600">
                                                            {{ $lesson->title }}
                                                        </a>
                                                    @else
                                                        <span class="font-medium text-gray-400 cursor-not-allowed">
                                                            {{ $lesson->title }}
                                                        </span>
                                                    @endif
                                                    @if($isCompleted)
                                                        <span class="text-green-600">✓</span>
                                                    @endif
                                                    @if($lesson->is_free)
                                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">FREE</span>
                                                    @elseif(!$enrollment && !$book->is_free)
                                                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">PAID</span>
                                                    @endif
                                                </div>
                                                @if($watchPercentage > 0 && !$isCompleted && $canAccess)
                                                    <div class="mt-1">
                                                        <div class="w-full bg-gray-200 rounded-full h-1">
                                                            <div class="bg-blue-600 h-1 rounded-full" style="width: {{ $watchPercentage }}%"></div>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-1">{{ $watchPercentage }}% watched</p>
                                                    </div>
                                                @endif
                                                @if(!$canAccess)
                                                    <p class="text-xs text-yellow-600 mt-1">
                                                        <a href="{{ route('student.courses.show', $book->id) }}" class="underline">Purchase course</a> to access
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 italic">No lessons available in this chapter</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-4 mb-4">
                <h3 class="font-bold mb-4">Progress</h3>
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
                    @if($book->teacher)
                        <p><span class="font-medium">Teacher:</span> {{ $book->teacher->name }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
