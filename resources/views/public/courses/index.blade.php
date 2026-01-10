@extends('layouts.app')

@php
    $seo = \App\Services\SEOService::generateMetaTags([
        'title' => 'All Courses - Kitabasan Learning Platform | Browse Online Courses',
        'description' => 'Browse our comprehensive collection of online courses. Find courses by grade, subject, or search by keywords. Free and paid courses available from expert instructors.',
        'keywords' => 'online courses, browse courses, all courses, course catalog, online learning courses, free courses, paid courses, course search, find courses',
        'url' => route('courses.index'),
    ]);

    $breadcrumbSchema = \App\Services\SEOService::generateBreadcrumbSchema([
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Courses', 'url' => route('courses.index')],
    ]);
@endphp

@section('title', $seo['title'])
@section('description', $seo['description'])
@section('keywords', $seo['keywords'])

@push('structured_data')
<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Explore Our Courses</h1>
            <p class="text-xl text-blue-100">Discover courses that match your interests and goals</p>
        </div>
    </section>

    <!-- Filters and Search -->
    <section class="bg-white shadow-sm py-6">
        <div class="container mx-auto px-4">
            <form method="GET" action="{{ route('courses.index') }}" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search courses..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <select name="grade" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Grades</option>
                        @foreach($grades ?? [] as $grade)
                            <option value="{{ $grade->id }}" {{ request('grade') == $grade->id ? 'selected' : '' }}>
                                {{ $grade->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="free" {{ request('type') == 'free' ? 'selected' : '' }}>Free</option>
                        <option value="paid" {{ request('type') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'grade', 'type']))
                    <a href="{{ route('courses.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 font-medium">
                        Clear
                    </a>
                @endif
            </form>
        </div>
    </section>

    <!-- Courses Grid -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            @if($courses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($courses as $course)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                        <div class="h-48 bg-gradient-to-br from-blue-400 to-indigo-600 relative">
                            @if($course->cover_image)
                                <img src="{{ \Storage::url($course->cover_image) }}"
                                     alt="{{ $course->title }} - Course Image"
                                     class="w-full h-full object-cover"
                                     loading="lazy"
                                     onload="this.classList.add('loaded')">
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
                            <div class="flex items-center justify-between mb-4">
                                @if($course->teacher)
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-blue-600 font-semibold text-xs">{{ substr($course->teacher->name, 0, 1) }}</span>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ $course->teacher->name }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    @if($course->is_free)
                                        <span class="text-green-600 font-bold text-lg">Free</span>
                                    @else
                                        <span class="text-blue-600 font-bold text-lg">Rs. {{ number_format($course->price, 0) }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('courses.show', $course->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12">
                    {{ $courses->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No courses found</h3>
                    <p class="text-gray-600 mb-6">Try adjusting your search or filters</p>
                    <a href="{{ route('courses.index') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                        View All Courses
                    </a>
                </div>
            @endif
        </div>
    </section>

    @include('partials.footer')
</div>
@endsection

