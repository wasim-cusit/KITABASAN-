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
           class="flex flex-col justify-center min-h-[72px] p-4 rounded-lg border-2 transition-all duration-200 {{ empty($activeStatus) ? 'border-blue-500 bg-blue-50 shadow-sm ring-2 ring-blue-200' : 'border-slate-200 bg-slate-50 hover:border-slate-300 hover:bg-slate-100' }}">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">All</span>
            <span class="text-xl font-bold text-slate-900 mt-0.5">{{ $statusSummary['total'] ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.courses.index', array_merge($baseQuery, ['status' => 'draft'])) }}"
           class="flex flex-col justify-center min-h-[72px] p-4 rounded-lg border-2 transition-all duration-200 {{ $activeStatus === 'draft' ? 'border-amber-500 bg-amber-50 shadow-sm ring-2 ring-amber-200' : 'border-amber-200 bg-amber-50/80 hover:border-amber-300 hover:bg-amber-100' }}">
            <span class="text-xs font-semibold uppercase tracking-wide text-amber-700">Draft</span>
            <span class="text-xl font-bold text-slate-900 mt-0.5">{{ $statusSummary['draft'] ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.courses.index', array_merge($baseQuery, ['status' => 'pending'])) }}"
           class="flex flex-col justify-center min-h-[72px] p-4 rounded-lg border-2 transition-all duration-200 {{ $activeStatus === 'pending' ? 'border-orange-500 bg-orange-50 shadow-sm ring-2 ring-orange-200' : 'border-orange-200 bg-orange-50/80 hover:border-orange-300 hover:bg-orange-100' }}">
            <span class="text-xs font-semibold uppercase tracking-wide text-orange-700">Pending</span>
            <span class="text-xl font-bold text-slate-900 mt-0.5">{{ $statusSummary['pending'] ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.courses.index', array_merge($baseQuery, ['status' => 'published'])) }}"
           class="flex flex-col justify-center min-h-[72px] p-4 rounded-lg border-2 transition-all duration-200 {{ $activeStatus === 'published' ? 'border-emerald-500 bg-emerald-50 shadow-sm ring-2 ring-emerald-200' : 'border-emerald-200 bg-emerald-50/80 hover:border-emerald-300 hover:bg-emerald-100' }}">
            <span class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Published</span>
            <span class="text-xl font-bold text-slate-900 mt-0.5">{{ $statusSummary['published'] ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.courses.index', array_merge($baseQuery, ['status' => 'approved'])) }}"
           class="flex flex-col justify-center min-h-[72px] p-4 rounded-lg border-2 transition-all duration-200 {{ $activeStatus === 'approved' ? 'border-green-500 bg-green-50 shadow-sm ring-2 ring-green-200' : 'border-green-200 bg-green-50/80 hover:border-green-300 hover:bg-green-100' }}">
            <span class="text-xs font-semibold uppercase tracking-wide text-green-700">Approved</span>
            <span class="text-xl font-bold text-slate-900 mt-0.5">{{ $statusSummary['approved'] ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.courses.index', array_merge($baseQuery, ['status' => 'rejected'])) }}"
           class="flex flex-col justify-center min-h-[72px] p-4 rounded-lg border-2 transition-all duration-200 {{ $activeStatus === 'rejected' ? 'border-rose-500 bg-rose-50 shadow-sm ring-2 ring-rose-200' : 'border-rose-200 bg-rose-50/80 hover:border-rose-300 hover:bg-rose-100' }}">
            <span class="text-xs font-semibold uppercase tracking-wide text-rose-700">Rejected</span>
            <span class="text-xl font-bold text-slate-900 mt-0.5">{{ $statusSummary['rejected'] ?? 0 }}</span>
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.courses.index') }}" class="mb-6 flex flex-wrap items-center gap-2">
        <input type="text" name="search" placeholder="Search courses..." value="{{ request('search') }}"
               class="px-4 py-2 border border-gray-300 rounded-lg text-sm w-48 min-w-0">
        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white w-36">
            <option value="">All Status</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <select name="subject_id" class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white w-48 min-w-0">
            <option value="">All Subjects</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" {{ (string) request('subject_id') === (string) $subject->id ? 'selected' : '' }}>
                    {{ $subject->grade?->name ?? 'N/A' }} - {{ $subject->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium">
            Filter
        </button>
        @if(request()->hasAny(['search', 'status', 'subject_id']))
            <a href="{{ route('admin.courses.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium">
                Clear
            </a>
        @endif
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
                                        {{-- Always show a fixed-size avatar box: image or initial --}}
                                        <div class="h-10 w-10 lg:h-12 lg:w-12 rounded-full shrink-0 mr-2 lg:mr-3 overflow-hidden flex items-center justify-center bg-gradient-to-br from-blue-400 to-indigo-600 text-white text-sm font-bold">
                                            @if($course->getCoverImageUrl())
                                                <img src="{{ $course->getCoverImageUrl() }}" alt="{{ $course->title }}" class="w-full h-full object-cover" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <span class="hidden w-full h-full flex items-center justify-center">{{ $course->getTitleInitial() }}</span>
                                            @else
                                                <span>{{ $course->getTitleInitial() }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.courses.show', $course->id) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">{{ $course->title }}</a>
                                            <div class="text-xs text-gray-500 lg:hidden">
                                                @if($course->teacher)
                                                    <a href="{{ route('admin.teachers.show', $course->teacher) }}" class="text-blue-600 hover:text-blue-800">{{ $course->teacher->name }}</a>
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 hidden lg:block">{{ Str::limit($course->description, 50) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                    @if($course->teacher)
                                        <a href="{{ route('admin.teachers.show', $course->teacher) }}" class="text-blue-600 hover:text-blue-800">{{ $course->teacher->name }}</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                    {{ $course->subject?->grade?->name ?? '' }} → {{ $course->subject?->name ?? 'N/A' }}
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
                                    <div class="flex flex-col sm:flex-row gap-1 sm:gap-0 items-start sm:items-center">
                                        <a href="{{ route('admin.courses.show', $course->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded text-blue-600 hover:text-blue-900 hover:bg-blue-50 sm:mr-1" title="View">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('admin.courses.edit', $course->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 sm:mr-1" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        @if($course->status == 'draft')
                                            <form action="{{ route('admin.courses.approve', $course->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded text-green-600 hover:text-green-900 hover:bg-green-50 sm:mr-1" title="Approve">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded text-red-600 hover:text-red-900 hover:bg-red-50" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
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
        {{ $courses->withQueryString()->links() }}
    </div>
</div>
@endsection

