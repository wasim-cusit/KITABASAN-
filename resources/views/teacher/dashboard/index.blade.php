@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="teacher-dashboard-container max-w-7xl mx-auto px-1 sm:px-3 lg:px-8">
    {{-- Welcome header --}}
    <div class="mb-4 lg:mb-6 bg-blue-50 border border-blue-100 rounded-lg sm:rounded-xl px-3 py-2 sm:px-4 sm:py-3 lg:px-6 lg:py-4">
        <h1 class="teacher-dashboard-welcome-title text-base sm:text-2xl lg:text-3xl leading-snug tracking-tight text-blue-900">
            Welcome back, {{ trim(Auth::user()->first_name . ' ' . Auth::user()->last_name) ?: Auth::user()->name }}
        </h1>
        <p class="teacher-dashboard-welcome-subtitle mt-1">Here’s an overview of your teaching activity.</p>
    </div>

    {{-- Stat cards --}}
    <div class="teacher-dashboard-stats grid grid-cols-2 lg:grid-cols-4 gap-2.5 lg:gap-5 mb-4 lg:mb-6">
        <div class="teacher-dashboard-stat-card bg-blue-50 rounded-md lg:rounded-lg shadow-sm border border-blue-100 p-2 sm:p-3 lg:p-4 flex items-center sm:items-start gap-2.5 sm:gap-3">
            <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 rounded-lg bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="teacher-dashboard-stat-label text-xs lg:text-sm">Total Courses</p>
                <p class="teacher-dashboard-stat-value text-xl lg:text-3xl mt-0.5">{{ $stats['total_courses'] }}</p>
            </div>
        </div>

        <div class="teacher-dashboard-stat-card bg-green-50 rounded-md lg:rounded-lg shadow-sm border border-green-100 p-2 sm:p-3 lg:p-4 flex items-center sm:items-start gap-2.5 sm:gap-3">
            <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 rounded-lg bg-green-50 flex items-center justify-center">
                <svg class="w-5 h-5 lg:w-6 lg:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="teacher-dashboard-stat-label text-xs lg:text-sm">Total Students</p>
                <p class="teacher-dashboard-stat-value text-xl lg:text-3xl mt-0.5">{{ $stats['total_students'] }}</p>
            </div>
        </div>

        <div class="teacher-dashboard-stat-card bg-indigo-50 rounded-md lg:rounded-lg shadow-sm border border-indigo-100 p-2 sm:p-3 lg:p-4 flex items-center sm:items-start gap-2.5 sm:gap-3">
            <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 rounded-lg bg-indigo-50 flex items-center justify-center">
                <svg class="w-5 h-5 lg:w-6 lg:h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="teacher-dashboard-stat-label text-xs lg:text-sm">Total Enrollments</p>
                <p class="teacher-dashboard-stat-value text-xl lg:text-3xl mt-0.5">{{ $stats['total_enrollments'] }}</p>
            </div>
        </div>

        <div class="teacher-dashboard-stat-card bg-amber-50 rounded-md lg:rounded-lg shadow-sm border border-amber-100 p-2 sm:p-3 lg:p-4 flex items-center sm:items-start gap-2.5 sm:gap-3">
            <div class="flex-shrink-0 w-10 h-10 lg:w-12 lg:h-12 rounded-lg bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 lg:w-6 lg:h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="teacher-dashboard-stat-label text-xs lg:text-sm">Pending Courses</p>
                <p class="teacher-dashboard-stat-value teacher-dashboard-stat-value--amber text-xl lg:text-3xl mt-0.5">{{ $stats['pending_courses'] }}</p>
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="teacher-dashboard-quick-actions flex flex-wrap gap-3 mb-6 lg:mb-8">
        <a href="{{ route('teacher.courses.create') }}" class="teacher-dashboard-quick-action teacher-dashboard-quick-action--primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Create Course
        </a>
        <a href="{{ route('teacher.courses.index') }}" class="teacher-dashboard-quick-action teacher-dashboard-quick-action--secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            View All Courses
        </a>
        <a href="{{ route('teacher.students.index') }}" class="teacher-dashboard-quick-action teacher-dashboard-quick-action--secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            Students
        </a>
        <a href="{{ route('teacher.chatbot.index') }}" class="teacher-dashboard-quick-action teacher-dashboard-quick-action--secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            Chatbot
        </a>
    </div>

    {{-- My Courses --}}
    <div class="teacher-dashboard-courses bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="teacher-dashboard-courses-header px-4 py-4 lg:px-6 lg:py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="teacher-dashboard-courses-title">My Courses</h2>
            <a href="{{ route('teacher.courses.index') }}" class="teacher-dashboard-courses-viewall">
                View all courses →
            </a>
        </div>
        <div class="teacher-dashboard-courses-list">
            @forelse($myCourses as $course)
            <div class="teacher-dashboard-course-row">
                <div class="teacher-dashboard-course-row-inner">
                    {{-- Thumbnail --}}
                    <a href="{{ route('teacher.courses.show', $course->id) }}" class="teacher-dashboard-course-thumb">
                        @if($course->hasValidCoverImage())
                            <img src="{{ $course->getCoverImageUrl() }}" alt="{{ $course->title }}" class="w-full h-full object-cover" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="hidden teacher-dashboard-course-thumb-placeholder w-full h-full absolute inset-0 flex items-center justify-center text-white text-2xl font-bold">{{ $course->getTitleInitial() }}</div>
                        @else
                            <div class="teacher-dashboard-course-thumb-placeholder w-full h-full flex items-center justify-center text-white text-2xl font-bold">
                                {{ $course->getTitleInitial() }}
                            </div>
                        @endif
                    </a>
                    {{-- Body: title, subject, enrollments --}}
                    <div class="teacher-dashboard-course-content">
                        <div class="teacher-dashboard-course-meta">
                            <a href="{{ route('teacher.courses.show', $course->id) }}" class="teacher-dashboard-course-title">
                                {{ $course->title }}
                            </a>
                            <p class="teacher-dashboard-course-subject">{{ optional($course->subject)->name ?? '—' }}</p>
                            <p class="teacher-dashboard-course-enrollments">{{ $course->enrollments->count() }} {{ Str::plural('enrollment', $course->enrollments->count()) }}</p>
                        </div>
                    </div>
                    {{-- Footer: status + actions --}}
                    <div class="teacher-dashboard-course-footer">
                        <span class="teacher-dashboard-course-status @if(in_array($course->status, ['approved', 'published'])) teacher-dashboard-course-status--success @elseif($course->status === 'pending') teacher-dashboard-course-status--warning @else teacher-dashboard-course-status--default @endif">
                            {{ ucfirst($course->status) }}
                        </span>
                        <div class="teacher-dashboard-course-actions">
                            <a href="{{ route('teacher.courses.show', $course->id) }}" class="teacher-dashboard-course-action">
                                Manage
                            </a>
                            <a href="{{ route('teacher.courses.edit', $course->id) }}" class="teacher-dashboard-course-action">
                                Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="teacher-dashboard-courses-empty px-4 py-12 lg:px-6 lg:py-16 text-center">
                <svg class="mx-auto h-12 w-12 lg:h-16 lg:w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="teacher-dashboard-empty-title mt-4">No courses yet</h3>
                <p class="teacher-dashboard-empty-desc mt-2 max-w-sm mx-auto">Create your first course to start teaching and managing enrollments.</p>
                <a href="{{ route('teacher.courses.create') }}" class="mt-6 inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create your first course
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
