@extends('layouts.admin')

@section('title', 'Courses Management')
@section('page-title', 'Courses Management')

@section('content')
<div class="bg-white rounded-lg shadow p-4 lg:p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <h2 class="text-xl lg:text-2xl font-bold">All Courses</h2>
        <a href="{{ route('admin.courses.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm lg:text-base text-center">
            Add New Course
        </a>
    </div>

    <!-- Status Summary -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6" data-testid="admin-courses-status-summary">
        @php
            $baseQuery = request()->except('page', 'status');
            $activeStatus = request('status');
        @endphp

        <a href="{{ route('admin.courses.index', $baseQuery) }}"
           class="p-3 rounded-lg border border-slate-200 bg-slate-50/70 hover:bg-slate-50 transition {{ empty($activeStatus) ? 'ring-2 ring-blue-300' : '' }}">
            <div class="text-xs text-slate-600 font-semibold">All</div>
            <div class="text-lg font-bold text-gray-900">{{ $statusSummary['total'] ?? 0 }}</div>
        </a>

        <a href="{{ route('admin.courses.index', array_merge($baseQuery, ['status' => 'draft'])) }}"
           class="p-3 rounded-lg border border-amber-200 bg-amber-50/70 hover:bg-amber-50 transition {{ $activeStatus === 'draft' ? 'ring-2 ring-amber-300' : '' }}">
            <div class="text-xs text-amber-700 font-semibold">Draft</div>
            <div class="text-lg font-bold text-gray-900">{{ $statusSummary['draft'] ?? 0 }}</div>
        </a>

        <a href="{{ route('admin.courses.index', array_merge($baseQuery, ['status' => 'pending'])) }}"
           class="p-3 rounded-lg border border-orange-200 bg-orange-50/70 hover:bg-orange-50 transition {{ $activeStatus === 'pending' ? 'ring-2 ring-orange-300' : '' }}">
            <div class="text-xs text-orange-700 font-semibold">Pending</div>
            <div class="text-lg font-bold text-gray-900">{{ $statusSummary['pending'] ?? 0 }}</div>
        </a>

        <a href="{{ route('admin.courses.index', array_merge($baseQuery, ['status' => 'published'])) }}"
           class="p-3 rounded-lg border border-emerald-200 bg-emerald-50/70 hover:bg-emerald-50 transition {{ $activeStatus === 'published' ? 'ring-2 ring-emerald-300' : '' }}">
            <div class="text-xs text-emerald-700 font-semibold">Published</div>
            <div class="text-lg font-bold text-gray-900">{{ $statusSummary['published'] ?? 0 }}</div>
        </a>

        <a href="{{ route('admin.courses.index', array_merge($baseQuery, ['status' => 'approved'])) }}"
           class="p-3 rounded-lg border border-green-200 bg-green-50/70 hover:bg-green-50 transition {{ $activeStatus === 'approved' ? 'ring-2 ring-green-300' : '' }}">
            <div class="text-xs text-green-700 font-semibold">Approved</div>
            <div class="text-lg font-bold text-gray-900">{{ $statusSummary['approved'] ?? 0 }}</div>
        </a>

        <a href="{{ route('admin.courses.index', array_merge($baseQuery, ['status' => 'rejected'])) }}"
           class="p-3 rounded-lg border border-rose-200 bg-rose-50/70 hover:bg-rose-50 transition {{ $activeStatus === 'rejected' ? 'ring-2 ring-rose-300' : '' }}">
            <div class="text-xs text-rose-700 font-semibold">Rejected</div>
            <div class="text-lg font-bold text-gray-900">{{ $statusSummary['rejected'] ?? 0 }}</div>
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.courses.index') }}" class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <input type="text" name="search" placeholder="Search courses..." value="{{ request('search') }}" 
               class="px-4 py-2 border rounded-lg">
        <select name="status" class="px-4 py-2 border rounded-lg">
            <option value="">All Status</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
        </select>
        <select name="subject_id" class="px-4 py-2 border rounded-lg">
            <option value="">All Subjects</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                    {{ $subject->grade->name }} - {{ $subject->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            Filter
        </button>
    </form>

    <!-- Courses Table -->
    <div class="overflow-x-auto -mx-4 lg:mx-0">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                            <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Teacher</th>
                            <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Subject</th>
                            <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($courses as $course)
                            <tr>
                                <td class="px-3 lg:px-6 py-4">
                                    <div class="flex items-center">
                                        @if($course->cover_image)
                                            <img src="{{ Storage::url($course->cover_image) }}" alt="{{ $course->title }}" class="h-10 w-10 lg:h-12 lg:w-12 rounded object-cover mr-2 lg:mr-3">
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                                            <div class="text-xs text-gray-500 lg:hidden">{{ $course->teacher->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500 hidden lg:block">{{ Str::limit($course->description, 50) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">{{ $course->teacher->name ?? 'N/A' }}</td>
                                <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                    {{ $course->subject->grade->name ?? '' }} → {{ $course->subject->name ?? 'N/A' }}
                                </td>
                                <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($course->is_free)
                                        <span class="text-green-600 font-semibold">Free</span>
                                    @else
                                        Rs. {{ number_format($course->price, 2) }}
                                    @endif
                                </td>
                                <td class="px-3 lg:px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $course->status == 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($course->status) }}
                                    </span>
                                </td>
                                <td class="px-3 lg:px-6 py-4 text-sm font-medium">
                                    <div class="flex flex-col sm:flex-row gap-1 sm:gap-0">
                                        <a href="{{ route('admin.courses.show', $course->id) }}" class="text-blue-600 hover:text-blue-900 sm:mr-3">View</a>
                                        <a href="{{ route('admin.courses.edit', $course->id) }}" class="text-indigo-600 hover:text-indigo-900 sm:mr-3">Edit</a>
                                        @if($course->status == 'draft')
                                            <form action="{{ route('admin.courses.approve', $course->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900 sm:mr-3">Approve</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No courses found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $courses->links() }}
    </div>
</div>
@endsection

