@extends('layouts.app')

@section('title', $course->title . ' - Kitabasan Learning Platform')

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
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <span class="text-blue-600 font-semibold">{{ substr($course->teacher->name, 0, 1) }}</span>
                        </div>
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
                        @if($course->cover_image)
                            <img src="{{ Storage::url($course->cover_image) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover rounded-lg mb-6">
                        @endif
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
                    <div class="h-40 bg-gradient-to-br from-blue-400 to-indigo-600">
                            @if($relatedCourse->cover_image)
                                <img src="{{ \Storage::url($relatedCourse->cover_image) }}" alt="{{ $relatedCourse->title }}" class="w-full h-full object-cover">
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

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="mb-4">
                        <img src="{{ asset('logo.jpeg') }}" alt="Kitabasan Logo" class="h-8">
                    </div>
                    <p class="text-gray-400">Your trusted learning platform for quality education.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                        <li><a href="{{ route('courses.index') }}" class="hover:text-white">Courses</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white">About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Help Center</a></li>
                        <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>Email: info@kitabasan.com</li>
                        <li>Phone: +92 300 1234567</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Kitabasan Learning Platform. All rights reserved.</p>
            </div>
        </div>
    </footer>
</div>
@endsection

