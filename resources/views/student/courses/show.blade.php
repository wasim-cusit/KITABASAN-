@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Course Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h1 class="text-3xl font-bold mb-4">{{ $course->title }}</h1>
                <p class="text-gray-600 mb-4">{{ $course->description }}</p>

                <div class="flex items-center gap-4 mb-4">
                    <span class="text-sm text-gray-500">{{ $course->subject->grade->name }} → {{ $course->subject->name }}</span>
                    @if($course->teacher)
                        <span class="text-sm text-gray-500">By {{ $course->teacher->name }}</span>
                    @endif
                </div>

                @if($course->cover_image)
                    <img src="{{ \Storage::url($course->cover_image) }}" alt="{{ $course->title }}" class="w-full h-64 object-cover rounded-lg mb-4">
                @endif
            </div>

            <!-- Course Content Preview -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold mb-4">Course Content</h2>

                @if($course->chapters->count() > 0)
                    <div class="space-y-4">
                        @foreach($course->chapters as $chapter)
                            <div class="border rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-semibold">{{ $chapter->title }}</h3>
                                    @if($chapter->is_free)
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">FREE</span>
                                    @elseif(!$enrollment && !$course->is_free)
                                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">PAID</span>
                                    @endif
                                </div>
                                @if($chapter->lessons->count() > 0)
                                    <div class="ml-4 space-y-1">
                                        @foreach($chapter->lessons as $lesson)
                                            <div class="flex items-center gap-2 text-sm">
                                                <span>• {{ $lesson->title }}</span>
                                                @if($lesson->is_free)
                                                    <span class="text-xs text-green-600">(Free)</span>
                                                @elseif(!$enrollment && !$course->is_free)
                                                    <span class="text-xs text-yellow-600">(Paid)</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Course content will be available soon.</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                <div class="text-center mb-6">
                    @if($course->is_free)
                        <div class="text-3xl font-bold text-green-600 mb-2">Free</div>
                    @else
                        <div class="text-3xl font-bold text-blue-600 mb-2">Rs. {{ number_format($course->price, 0) }}</div>
                    @endif
                </div>

                @if($enrollment)
                    <a href="{{ route('student.learning.index', $course->id) }}"
                       class="block w-full bg-blue-600 text-white text-center px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold mb-3">
                        Continue Learning
                    </a>
                    <p class="text-xs text-gray-500 text-center">
                        Enrolled on {{ $enrollment->enrolled_at->format('M d, Y') }}
                    </p>
                @else
                    @if($course->is_free)
                        <form action="{{ route('student.courses.enroll', $course->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-semibold mb-3">
                                Enroll for Free
                            </button>
                        </form>
                    @else
                        <form action="{{ route('student.courses.enroll', $course->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold mb-3">
                                Purchase Course
                            </button>
                        </form>
                    @endif

                    @if($freeChapters > 0 || $freeLessons > 0)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-3">
                            <p class="text-sm text-green-800 font-semibold mb-1">Free Preview Available!</p>
                            <p class="text-xs text-green-700">
                                {{ $freeChapters }} free chapters and {{ $freeLessons }} free lessons available after login.
                            </p>
                        </div>
                    @endif
                @endif

                <div class="border-t pt-4 mt-4">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Chapters:</span>
                            <span class="font-semibold">{{ $course->chapters->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Lessons:</span>
                            <span class="font-semibold">{{ $totalLessons }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Duration:</span>
                            <span class="font-semibold">{{ $course->duration_months }} months</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

