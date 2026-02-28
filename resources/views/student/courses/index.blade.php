@extends('layouts.student')

@section('title', 'Browse Courses')
@section('page-title', 'Browse Courses')

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 items-stretch">
            @foreach($courses as $course)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow flex flex-col h-full self-stretch">
                    <div class="h-40 lg:h-48 bg-gradient-to-br from-blue-400 to-indigo-600 relative overflow-hidden">
                        @if($course->hasValidCoverImage())
                            <img src="{{ $course->getCoverImageUrl() }}" alt="{{ $course->title }}" class="w-full h-full object-cover" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="hidden w-full h-full absolute inset-0 flex items-center justify-center text-white text-4xl font-bold">{{ $course->getTitleInitial() }}</div>
                        @else
                            <div class="w-full h-full flex items-center justify-center text-white text-4xl font-bold">{{ $course->getTitleInitial() }}</div>
                        @endif
                        @if(!empty($purchasedCourseIds) && $purchasedCourseIds->contains($course->id))
                            <span class="absolute top-2 left-2 lg:top-4 lg:left-4 bg-emerald-600 text-white px-2 lg:px-3 py-1 rounded-full text-xs lg:text-sm font-semibold inline-flex items-center gap-1">
                                <svg class="w-3 h-3 lg:w-4 lg:h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.704 5.293a1 1 0 010 1.414l-7.5 7.5a1 1 0 01-1.414 0l-3.5-3.5a1 1 0 011.414-1.414L9 11.086l6.793-6.793a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Purchased
                            </span>
                        @endif
                        @if($course->is_free)
                            <span class="absolute top-2 right-2 lg:top-4 lg:right-4 bg-green-500 text-white px-2 lg:px-3 py-1 rounded-full text-xs lg:text-sm font-semibold">FREE</span>
                        @else
                            <span class="absolute top-2 right-2 lg:top-4 lg:right-4 bg-blue-600 text-white px-2 lg:px-3 py-1 rounded-full text-xs lg:text-sm font-semibold">PAID</span>
                        @endif
                    </div>
                    <div class="p-4 lg:p-6 flex flex-col flex-1">
                        <div class="min-h-[7.5rem] lg:min-h-[8.5rem]">
                            <div class="flex items-center mb-2 text-xs lg:text-sm text-gray-500">
                                <span>{{ $course->subject->grade->name ?? 'General' }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ $course->subject->name ?? 'Course' }}</span>
                            </div>
                            <h3 class="text-base lg:text-xl font-bold text-gray-900 mb-2 line-clamp-2 min-h-[2.75rem] lg:min-h-[3.25rem]">{{ $course->title }}</h3>
                            <p class="text-gray-600 text-xs lg:text-sm mb-3 lg:mb-4 line-clamp-2 min-h-[2.5rem] lg:min-h-[2.75rem]">{{ Str::limit($course->description, 80) }}</p>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mt-auto">
                            <div>
                                @if($course->is_free)
                                    <span class="text-green-600 font-bold text-sm lg:text-base">Free</span>
                                @else
                                    <span class="text-blue-600 font-bold text-sm lg:text-base">Rs. {{ number_format($course->price, 0) }}</span>
                                @endif
                            </div>
                            <a href="{{ route('student.courses.show', $course->id) }}" class="bg-blue-600 text-white px-3 lg:px-4 py-2 rounded-lg hover:bg-blue-700 transition text-xs lg:text-sm font-medium text-center">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 lg:mt-8">
            {{ $courses->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6 lg:p-12 text-center">
            <p class="text-gray-500 text-base lg:text-lg">No courses available at the moment.</p>
        </div>
    @endif
</div>
@endsection

