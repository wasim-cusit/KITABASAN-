@extends('layouts.teacher')

@section('title', 'My Courses')
@section('page-title', 'My Courses')

@section('content')
<div class="teacher-courses-container">
    <div class="teacher-courses-header">
        <h1 class="teacher-courses-header-title">My Courses</h1>
        <a href="{{ route('teacher.courses.create') }}" class="teacher-courses-create-btn">
            Create New Course
        </a>
    </div>

    @if($courses->count() > 0)
        <div class="teacher-courses-grid">
            @foreach($courses as $course)
                <div class="teacher-courses-card">
                    <div class="teacher-courses-card-cover">
                        @if($course->hasValidCoverImage())
                            <img src="{{ $course->getCoverImageUrl() }}" alt="{{ $course->title }}" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="hidden w-full h-full absolute inset-0 flex items-center justify-center bg-gradient-to-br from-blue-400 to-indigo-600 text-white text-4xl font-bold">{{ $course->getTitleInitial() }}</div>
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-400 to-indigo-600 text-white text-4xl font-bold">{{ $course->getTitleInitial() }}</div>
                        @endif
                        <span class="teacher-courses-card-status {{ in_array($course->status, ['published', 'approved']) ? 'teacher-courses-card-status--published' : 'teacher-courses-card-status--draft' }}">
                            {{ ucfirst($course->status) }}
                        </span>
                    </div>
                    <div class="teacher-courses-card-body">
                        <div class="teacher-courses-card-content">
                            <h3 class="teacher-courses-card-title">{{ $course->title }}</h3>
                            <p class="teacher-courses-card-desc">{{ Str::limit($course->description, 80) }}</p>
                        </div>
                        <div class="teacher-courses-card-meta">
                            <span class="teacher-courses-card-grade">{{ optional(optional($course->subject)->grade)->name ?? '—' }} → {{ optional($course->subject)->name ?? '—' }}</span>
                            <span class="teacher-courses-card-price {{ $course->is_free ? 'teacher-courses-card-price--free' : 'teacher-courses-card-price--paid' }}">
                                {{ $course->is_free ? 'Free' : 'Rs. ' . number_format($course->price, 0) }}
                            </span>
                        </div>
                        <div class="teacher-courses-card-actions">
                            <a href="{{ route('teacher.courses.show', $course->id) }}" class="teacher-courses-card-btn teacher-courses-card-btn--primary">
                                Manage
                            </a>
                            <a href="{{ route('teacher.courses.edit', $course->id) }}" class="teacher-courses-card-btn teacher-courses-card-btn--secondary">
                                Edit
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="teacher-courses-pagination">
            {{ $courses->links() }}
        </div>
    @else
        <div class="teacher-courses-empty">
            <svg class="teacher-courses-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <h3 class="teacher-courses-empty-title">No courses yet</h3>
            <p class="teacher-courses-empty-desc">Create your first course to start teaching</p>
            <a href="{{ route('teacher.courses.create') }}" class="teacher-courses-create-btn">
                Create Your First Course
            </a>
        </div>
    @endif
</div>
@endsection

