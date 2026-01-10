@extends('layouts.app')

@section('title', 'Home - Kitabasan Learning Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white py-20 md:py-32">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                Learn Anything,<br>Anytime, Anywhere
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-blue-100 max-w-2xl mx-auto">
                Join thousands of students learning from expert teachers. Access premium courses and advance your skills.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('courses.index') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition shadow-lg">
                    Browse Courses
                </a>
                <a href="{{ route('register') }}" class="bg-blue-500 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-blue-400 transition border-2 border-white">
                    Start Learning Free
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold text-blue-600 mb-2">{{ number_format($totalCourses ?? 0) }}+</div>
                    <div class="text-gray-600 text-lg">Courses Available</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-blue-600 mb-2">{{ number_format($totalStudents ?? 0) }}+</div>
                    <div class="text-gray-600 text-lg">Active Students</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-blue-600 mb-2">{{ number_format($totalTeachers ?? 0) }}+</div>
                    <div class="text-gray-600 text-lg">Expert Teachers</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Courses -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Featured Courses</h2>
                <p class="text-gray-600 text-lg">Handpicked courses from our best instructors</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($featuredCourses ?? [] as $course)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="h-48 bg-gradient-to-br from-blue-400 to-indigo-600 relative">
                        @if($course->cover_image)
                            <img src="{{ \Storage::url($course->cover_image) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        @endif
                        @if($course->is_free)
                            <span class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">FREE</span>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-2">
                            <span class="text-sm text-gray-500">{{ $course->subject->grade->name ?? 'General' }}</span>
                            <span class="mx-2 text-gray-300">•</span>
                            <span class="text-sm text-gray-500">{{ $course->subject->name ?? 'Course' }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">{{ $course->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($course->description, 100) }}</p>
                        <div class="flex items-center justify-between">
                            <div>
                                @if($course->is_free)
                                    <span class="text-green-600 font-bold text-lg">Free</span>
                                @else
                                    <span class="text-blue-600 font-bold text-lg">Rs. {{ number_format($course->price, 0) }}</span>
                                @endif
                            </div>
                            <a href="{{ route('courses.show', $course->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                View Course
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">No featured courses available at the moment.</p>
                </div>
                @endforelse
            </div>
            <div class="text-center mt-12">
                <a href="{{ route('courses.index') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    View All Courses
                </a>
            </div>
        </div>
    </section>

    <!-- Free Courses -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Free Courses</h2>
                <p class="text-gray-600 text-lg">Start learning today with our free courses</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($freeCourses ?? [] as $course)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow border-2 border-green-200">
                    <div class="h-48 bg-gradient-to-br from-green-400 to-emerald-600 relative">
                        @if($course->cover_image)
                            <img src="{{ \Storage::url($course->cover_image) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        @endif
                        <span class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">FREE</span>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-2">
                            <span class="text-sm text-gray-500">{{ $course->subject->grade->name ?? 'General' }}</span>
                            <span class="mx-2 text-gray-300">•</span>
                            <span class="text-sm text-gray-500">{{ $course->subject->name ?? 'Course' }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">{{ $course->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($course->description, 100) }}</p>
                        <a href="{{ route('courses.show', $course->id) }}" class="inline-block w-full text-center bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium">
                            Start Learning Free
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">No free courses available at the moment.</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gradient-to-br from-gray-50 to-blue-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Why Choose Kitabasan?</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-md text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Expert Teachers</h3>
                    <p class="text-gray-600">Learn from industry experts and experienced educators</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-md text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Flexible Learning</h3>
                    <p class="text-gray-600">Study at your own pace, anytime and anywhere</p>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-md text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Track Progress</h3>
                    <p class="text-gray-600">Monitor your learning journey and achievements</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-blue-600 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready to Start Learning?</h2>
            <p class="text-xl mb-8 text-blue-100">Join thousands of students already learning with us</p>
            <a href="{{ route('register') }}" class="inline-block bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition shadow-lg">
                Get Started Today
            </a>
        </div>
    </section>

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
