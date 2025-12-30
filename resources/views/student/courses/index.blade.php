@extends('layouts.app')

@section('title', 'Browse Courses')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Browse Courses</h1>

    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="h-48 bg-gradient-to-br from-blue-400 to-indigo-600 relative">
                        @if($course->cover_image)
                            <img src="{{ \Storage::url($course->cover_image) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        @endif
                        @if($course->is_free)
                            <span class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">FREE</span>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-2 text-sm text-gray-500">
                            <span>{{ $course->subject->grade->name ?? 'General' }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $course->subject->name ?? 'Course' }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">{{ $course->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($course->description, 100) }}</p>
                        <div class="flex items-center justify-between">
                            <div>
                                @if($course->is_free)
                                    <span class="text-green-600 font-bold">Free</span>
                                @else
                                    <span class="text-blue-600 font-bold">Rs. {{ number_format($course->price, 0) }}</span>
                                @endif
                            </div>
                            <a href="{{ route('student.courses.show', $course->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $courses->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <p class="text-gray-500 text-lg">No courses available at the moment.</p>
        </div>
    @endif
</div>
@endsection

