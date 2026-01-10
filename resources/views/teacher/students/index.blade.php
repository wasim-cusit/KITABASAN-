@extends('layouts.teacher')

@section('title', 'My Students')
@section('page-title', 'My Students')

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    <div class="bg-white rounded-lg shadow p-4 lg:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h2 class="text-xl lg:text-2xl font-bold">My Students</h2>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Search Filter -->
        <form method="GET" action="{{ route('teacher.students.index') }}" class="mb-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <input type="text" name="search" placeholder="Search by name, email, or mobile..." value="{{ request('search') }}"
                       class="flex-1 px-4 py-2 border rounded-lg text-sm lg:text-base">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm lg:text-base">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('teacher.students.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-sm lg:text-base text-center">
                        Clear
                    </a>
                @endif
            </div>
        </form>

        <!-- Students Table -->
        <div class="overflow-x-auto -mx-4 lg:mx-0">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Email</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Mobile</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Courses</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($students as $student)
                                <tr>
                                    <td class="px-3 lg:px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 lg:h-10 lg:w-10">
                                                <img class="h-8 w-8 lg:h-10 lg:w-10 rounded-full" src="{{ $student->profile_image ? Storage::url($student->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) }}" alt="">
                                            </div>
                                            <div class="ml-2 lg:ml-4">
                                                <div class="text-xs lg:text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                                <div class="text-xs text-gray-500 sm:hidden">{{ Str::limit($student->email, 25) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-xs lg:text-sm text-gray-500 hidden sm:table-cell">{{ $student->email }}</td>
                                    <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-xs lg:text-sm text-gray-500 hidden md:table-cell">{{ $student->mobile ?? 'N/A' }}</td>
                                    <td class="px-3 lg:px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $student->enrollments_count }} {{ Str::plural('course', $student->enrollments_count) }}
                                        </span>
                                    </td>
                                    <td class="px-3 lg:px-6 py-4 text-xs lg:text-sm font-medium">
                                        <a href="{{ route('teacher.students.show', $student->id) }}" class="text-blue-600 hover:text-blue-900">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 text-sm lg:text-base">
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
        <div class="mt-4">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection

