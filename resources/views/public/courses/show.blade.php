@extends('layouts.app')

@php
    use Illuminate\Support\Str;
    $metaTitle = $course->meta_title ?? $course->title . ' - KITAB ASAN';
    $metaDescription = $course->meta_description ?? Str::limit($course->description ?? $course->short_description ?? 'Learn ' . $course->title . ' online with expert instructors.', 160);
    $metaKeywords = $course->meta_keywords ? implode(', ', $course->meta_keywords) : ($course->tags ? implode(', ', $course->tags) : 'online course, ' . $course->title . ', ' . ($course->subject->name ?? '') . ', ' . ($course->subject->grade->name ?? ''));
    $ogImage = $course->cover_image ? route('storage.serve', ['path' => ltrim(str_replace('\\', '/', $course->cover_image), '/')]) : asset('logo.jpeg');

    $seo = \App\Services\SEOService::generateMetaTags([
        'title' => $metaTitle,
        'description' => $metaDescription,
        'keywords' => $metaKeywords,
        'image' => $ogImage,
        'url' => route('courses.show', $course->id),
        'type' => 'article',
    ]);

    $courseSchema = \App\Services\SEOService::generateCourseSchema($course);
    $breadcrumbSchema = \App\Services\SEOService::generateBreadcrumbSchema([
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Courses', 'url' => route('courses.index')],
        ['name' => $course->title, 'url' => route('courses.show', $course->id)],
    ]);
@endphp

@section('title', $metaTitle)
@section('description', $metaDescription)
@section('keywords', $metaKeywords)
@section('og_image', $ogImage)

@push('structured_data')
<script type="application/ld+json">
{!! json_encode($courseSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Course Header -->
    <section class="bg-white py-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-8">
                <div class="lg:w-2/3">
                    <div class="mb-4">
                        <span class="text-sm text-gray-500">{{ $course->subject->grade->name ?? 'General' }}</span>
                        <span class="mx-2 text-gray-300">•</span>
                        <span class="text-sm text-gray-500">{{ $course->subject->name ?? 'Course' }}</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $course->title }}</h1>
                    <p class="text-lg text-gray-600 mb-6">{{ $course->description }}</p>

                    @if($course->teacher)
                    <div class="flex items-center mb-6">
                        <x-user-avatar :user="$course->teacher" size="lg" class="mr-4 border-2 border-blue-100" />
                        <div>
                            <div class="font-semibold text-gray-900">Instructor</div>
                            <div class="text-gray-600">{{ $course->teacher->name }}</div>
                        </div>
                    </div>
                    @endif

                    <div class="flex flex-wrap gap-4">
                        @if($course->is_free)
                            <span class="bg-green-100 text-green-800 px-4 py-2 rounded-lg font-semibold">Free Course</span>
                        @else
                            <span class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-semibold">Rs. {{ number_format($course->price, 0) }}</span>
                        @endif
                        @if($course->duration_months)
                            <span class="bg-gray-100 text-gray-800 px-4 py-2 rounded-lg">Access for {{ $course->duration_months }} months</span>
                        @endif
                    </div>
                </div>
                <div class="lg:w-1/3">
                    <div class="bg-white border-2 border-gray-200 rounded-xl p-6 sticky top-24">
                        <div class="w-full h-48 rounded-lg mb-6 overflow-hidden flex items-center justify-center">
                            @if($course->hasValidCoverImage())
                                <img src="{{ $course->getCoverImageUrl() }}"
                                     alt="{{ $course->title }} - Course Cover Image"
                                     class="w-full h-full object-cover"
                                     loading="lazy"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="hidden w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-400 to-indigo-600 text-white text-5xl font-bold">
                                    {{ $course->getTitleInitial() }}
                                </div>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-400 to-indigo-600 text-white text-5xl font-bold">
                                    {{ $course->getTitleInitial() }}
                                </div>
                            @endif
                        </div>
                        <div class="text-center mb-6">
                            @if($course->is_free)
                                <div class="text-3xl font-bold text-green-600 mb-2">Free</div>
                            @else
                                <div class="text-3xl font-bold text-blue-600 mb-2">Rs. {{ number_format($course->price, 0) }}</div>
                            @endif
                        </div>
                        @auth
                            @if(auth()->user()->hasRole('student'))
                                <a href="{{ route('student.courses.show', $course->id) }}" class="block w-full bg-blue-600 text-white text-center px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold mb-3">
                                    View Course
                                </a>
                            @endif
                        @else
                            <a href="{{ route('register') }}" class="block w-full bg-blue-600 text-white text-center px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold mb-3">
                                Enroll Now
                            </a>
                        @endauth
                        <a href="{{ route('courses.index') }}" class="block w-full bg-gray-200 text-gray-700 text-center px-6 py-3 rounded-lg hover:bg-gray-300 font-semibold">
                            Browse More Courses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Course Content -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-xl shadow-md p-8">
                <h2 class="text-2xl font-bold mb-6">Course Content</h2>
                <div class="space-y-4">
                    @forelse($course->chapters as $chapter)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-lg mb-2">{{ $chapter->title }}</h3>
                            @if($chapter->description)
                                <p class="text-gray-600 text-sm mb-3">{{ $chapter->description }}</p>
                            @endif
                            @if($chapter->lessons->count() > 0)
                                <div class="ml-4 space-y-2">
                                    @foreach($chapter->lessons as $lesson)
                                        <div class="flex items-center text-gray-700">
                                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>{{ $lesson->title }}</span>
                                            @if($lesson->is_free)
                                                <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Free</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500">Course content will be available soon.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <!-- Related Courses -->
    @if($relatedCourses->count() > 0)
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold mb-6">Related Courses</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedCourses as $relatedCourse)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="h-40 bg-gradient-to-br from-blue-400 to-indigo-600 relative overflow-hidden">
                            @if($relatedCourse->hasValidCoverImage())
                                <img src="{{ $relatedCourse->getCoverImageUrl() }}"
                                     alt="{{ $relatedCourse->title }} - Related Course Image"
                                     class="w-full h-full object-cover"
                                     loading="lazy"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="hidden w-full h-full absolute inset-0 flex items-center justify-center text-white text-4xl font-bold">{{ $relatedCourse->getTitleInitial() }}</div>
                            @else
                                <div class="w-full h-full flex items-center justify-center text-white text-4xl font-bold">{{ $relatedCourse->getTitleInitial() }}</div>
                            @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">{{ $relatedCourse->title }}</h3>
                        <div class="flex items-center justify-between">
                            @if($relatedCourse->is_free)
                                <span class="text-green-600 font-bold">Free</span>
                            @else
                                <span class="text-blue-600 font-bold">Rs. {{ number_format($relatedCourse->price, 0) }}</span>
                            @endif
                            <a href="{{ route('courses.show', $relatedCourse->id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View →</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @include('partials.footer')
</div>
@endsection

