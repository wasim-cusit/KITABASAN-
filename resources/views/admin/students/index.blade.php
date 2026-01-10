@extends('layouts.admin')

@section('title', 'Students Management')
@section('page-title', 'Students Management')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 lg:p-6">
        <!-- Header and Action Button -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-bold">Students Management</h2>
                <p class="text-sm text-gray-600 mt-1">
                    Total: {{ $stats['total'] ?? 0 }} | Active: {{ $stats['active'] ?? 0 }} | Enrolled: {{ $stats['enrolled'] ?? 0 }}
                </p>
            </div>
            <a href="{{ route('admin.students.create') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm lg:text-base text-center whitespace-nowrap">
                + Add New Student
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Students</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $stats['active'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Active</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-gray-600">{{ $stats['inactive'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Inactive</div>
            </div>
            <div class="bg-red-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-red-600">{{ $stats['suspended'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Suspended</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['enrolled'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Enrolled</div>
            </div>
            <div class="bg-orange-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-orange-600">{{ $stats['not_enrolled'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Not Enrolled</div>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('admin.students.index') }}" class="mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <input type="text" name="search" placeholder="Search by name, email, mobile..." value="{{ request('search') }}" 
                       class="px-4 py-2 border rounded-lg">
                
                <select name="status" class="px-4 py-2 border rounded-lg">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>

                <select name="enrollment_status" class="px-4 py-2 border rounded-lg">
                    <option value="">All Enrollment Status</option>
                    <option value="enrolled" {{ request('enrollment_status') == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                    <option value="not_enrolled" {{ request('enrollment_status') == 'not_enrolled' ? 'selected' : '' }}>Not Enrolled</option>
                </select>

                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Filter
                </button>
            </div>
        </form>

        <!-- Students Table -->
        <div class="overflow-x-auto -mx-4 lg:mx-0">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Email</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Mobile</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Enrollments</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden xl:table-cell">Payments</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($students as $student)
                                <tr>
                                    <td class="px-3 lg:px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 lg:h-10 lg:w-10">
                                                <img class="h-8 w-8 lg:h-10 lg:w-10 rounded-full" 
                                                     src="{{ $student->profile_image ? \Storage::url($student->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) }}" 
                                                     alt="{{ $student->name }}">
                                            </div>
                                            <div class="ml-2 lg:ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                                <div class="text-xs text-gray-500 sm:hidden">{{ \Str::limit($student->email, 25) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">{{ $student->email }}</td>
                                    <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">{{ $student->mobile ?? 'N/A' }}</td>
                                    <td class="px-3 lg:px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $student->status == 'active' ? 'bg-green-100 text-green-800' : ($student->status == 'suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                            {{ $student->enrollments_count ?? 0 }} course(s)
                                        </span>
                                    </td>
                                    <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden xl:table-cell">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                            {{ $student->payments_count ?? 0 }} payment(s)
                                        </span>
                                    </td>
                                    <td class="px-3 lg:px-6 py-4 text-sm font-medium">
                                        <div class="flex flex-col sm:flex-row gap-1 sm:gap-0">
                                            <a href="{{ route('admin.students.show', $student->id) }}" 
                                               class="text-blue-600 hover:text-blue-900 sm:mr-3">View</a>
                                            <a href="{{ route('admin.students.edit', $student->id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 sm:mr-3">Edit</a>
                                            <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this student?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No students found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $students->appends(request()->except('page'))->links() }}
        </div>
    </div>
</div>
@endsection
