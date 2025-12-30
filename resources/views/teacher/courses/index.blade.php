@extends('layouts.teacher')

@section('title', 'My Courses')
@section('page-title', 'My Courses')

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <h1 class="text-2xl lg:text-3xl font-bold">My Courses</h1>
        <a href="{{ route('teacher.courses.create') }}" class="bg-blue-600 text-white px-4 lg:px-6 py-2 rounded-lg hover:bg-blue-700 text-sm lg:text-base text-center">
            Create New Course
        </a>
    </div>

    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
            @foreach($courses as $course)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="h-40 lg:h-48 bg-gradient-to-br from-blue-400 to-indigo-600 relative">
                        @if($course->cover_image)
                            <img src="{{ \Storage::url($course->cover_image) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        @endif
                        <span class="absolute top-2 right-2 lg:top-4 lg:right-4 px-2 py-1 rounded text-xs font-semibold {{ $course->status === 'published' ? 'bg-green-500 text-white' : 'bg-yellow-500 text-white' }}">
                            {{ ucfirst($course->status) }}
                        </span>
                    </div>
                    <div class="p-4 lg:p-6">
                        <h3 class="text-lg lg:text-xl font-bold mb-2">{{ $course->title }}</h3>
                        <p class="text-xs lg:text-sm text-gray-600 mb-3 lg:mb-4">{{ Str::limit($course->description, 80) }}</p>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3 lg:mb-4 gap-2">
                            <span class="text-xs lg:text-sm text-gray-500">{{ $course->subject->grade->name }} → {{ $course->subject->name }}</span>
                            <span class="text-xs lg:text-sm font-semibold {{ $course->is_free ? 'text-green-600' : 'text-blue-600' }}">
                                {{ $course->is_free ? 'Free' : 'Rs. ' . number_format($course->price, 0) }}
                            </span>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <a href="{{ route('teacher.courses.show', $course->id) }}" class="flex-1 bg-blue-600 text-white text-center px-3 lg:px-4 py-2 rounded-lg hover:bg-blue-700 text-sm lg:text-base">
                                Manage
                            </a>
                            <a href="{{ route('teacher.courses.edit', $course->id) }}" class="flex-1 bg-gray-200 text-gray-700 text-center px-3 lg:px-4 py-2 rounded-lg hover:bg-gray-300 text-sm lg:text-base">
                                Edit
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $courses->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6 lg:p-12 text-center">
            <svg class="mx-auto h-16 w-16 lg:h-24 lg:w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2">No courses yet</h3>
            <p class="text-sm lg:text-base text-gray-600 mb-4 lg:mb-6">Create your first course to start teaching</p>
            <a href="{{ route('teacher.courses.create') }}" class="inline-block bg-blue-600 text-white px-4 lg:px-6 py-2 lg:py-3 rounded-lg hover:bg-blue-700 text-sm lg:text-base">
                Create Your First Course
            </a>
        </div>
    @endif
</div>
@endsection

