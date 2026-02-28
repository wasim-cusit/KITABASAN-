@extends('layouts.admin')

@section('title', 'Course Details')
@section('page-title', 'Course Details')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Course Info -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold">{{ $course->title }}</h2>
                <p class="text-gray-600">{{ $course->subject->grade->name }} → {{ $course->subject->name }}</p>
            </div>
            <div class="flex gap-2">
                @if($course->status == 'draft')
                    <form action="{{ route('admin.courses.approve', $course->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                            Approve & Publish
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.courses.edit', $course->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Edit Course
                </a>
            </div>
        </div>

        @if($course->hasValidCoverImage())
            <img src="{{ $course->getCoverImageUrl() }}" alt="{{ $course->title }}" class="w-full h-64 object-cover rounded-lg mb-6" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="hidden w-full h-64 flex items-center justify-center bg-gradient-to-br from-blue-400 to-indigo-600 text-white text-5xl font-bold rounded-lg mb-6">{{ $course->getTitleInitial() }}</div>
        @else
            <div class="w-full h-64 flex items-center justify-center bg-gradient-to-br from-blue-400 to-indigo-600 text-white text-5xl font-bold rounded-lg mb-6">{{ $course->getTitleInitial() }}</div>
        @endif

        <div class="grid grid-cols-3 gap-4 mb-6">
            <div>
                <span class="text-sm text-gray-500">Status</span>
                <p class="font-semibold">
                    <span class="px-2 py-1 text-xs rounded {{ $course->status == 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($course->status) }}
                    </span>
                </p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Price</span>
                <p class="font-semibold">
                    @if($course->is_free)
                        <span class="text-green-600">Free</span>
                    @else
                        Rs. {{ number_format($course->price, 2) }}
                    @endif
                </p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Duration</span>
                <p class="font-semibold">{{ $course->duration_months }} months</p>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-bold mb-2">Description</h3>
            <p class="text-gray-700">{{ $course->description }}</p>
        </div>

        <!-- Course Content -->
        <div>
            <h3 class="text-lg font-bold mb-4">Course Content</h3>
            <div class="space-y-4">
                @forelse($course->chapters as $chapter)
                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold mb-2">{{ $chapter->title }}</h4>
                        @if($chapter->lessons->count() > 0)
                            <div class="ml-4 space-y-1">
                                @foreach($chapter->lessons as $lesson)
                                    <div class="text-sm text-gray-600">• {{ $lesson->title }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500">No chapters yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4">Course Info</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Teacher:</span>
                    <span class="font-semibold">{{ $course->teacher->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Chapters:</span>
                    <span class="font-semibold">{{ $course->chapters->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Enrollments:</span>
                    <span class="font-semibold">{{ $course->enrollments->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Created:</span>
                    <span class="font-semibold">{{ $course->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

