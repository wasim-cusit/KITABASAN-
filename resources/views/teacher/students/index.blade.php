@extends('layouts.teacher')

@section('title', 'My Students')
@section('page-title', 'My Students')

@section('content')
<div class="teacher-students-container">
    <div class="teacher-students-card">
        <div class="teacher-students-header">
            <h2 class="teacher-students-header-title">My Students</h2>
        </div>

        <!-- Search Filter -->
        <form method="GET" action="{{ route('teacher.students.index') }}" class="teacher-students-search">
            <div class="teacher-students-search-inner">
                <input type="text" name="search" placeholder="Search by name, email, or mobile..." value="{{ request('search') }}"
                       class="teacher-students-search-input">
                <button type="submit" class="teacher-students-search-btn">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('teacher.students.index') }}" class="teacher-students-clear-btn">
                        Clear
                    </a>
                @endif
            </div>
        </form>

        <!-- Students Table -->
        <div class="teacher-students-table-wrap">
            <div class="teacher-students-table-inner">
                <div class="teacher-students-table-outer">
                    <table class="teacher-students-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th class="hide-sm">Email</th>
                                <th class="hide-md">Mobile</th>
                                <th>Courses</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td>
                                        <div class="teacher-students-cell-student">
                                            <div class="teacher-students-avatar">
                                                <img src="{{ $student->profile_image ? Storage::url($student->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) }}" alt="">
                                            </div>
                                            <div class="teacher-students-name-wrap">
                                                <div class="teacher-students-name">{{ $student->name }}</div>
                                                <div class="teacher-students-email-mobile">{{ Str::limit($student->email, 25) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap hide-sm teacher-students-cell-muted">{{ $student->email }}</td>
                                    <td class="whitespace-nowrap hide-md teacher-students-cell-muted">{{ $student->mobile ?? 'N/A' }}</td>
                                    <td>
                                        <span class="teacher-students-badge">
                                            {{ $student->enrollments_count }} {{ Str::plural('course', $student->enrollments_count) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('teacher.students.show', $student->id) }}" class="teacher-students-view-link">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="teacher-students-empty">
                                        No students found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="teacher-students-pagination">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection

