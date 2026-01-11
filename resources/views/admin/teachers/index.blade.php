@extends('layouts.admin')

@section('title', 'Teachers Management')
@section('page-title', 'Teachers Management')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 lg:p-6">
        <!-- Header and Action Button -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-bold">Teachers Management</h2>
                <p class="text-sm text-gray-600 mt-1">
                    Active: {{ $stats['active'] ?? 0 }} | Total: {{ $stats['total'] ?? 0 }}
                </p>
            </div>
            <a href="{{ route('admin.teachers.create') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm lg:text-base text-center whitespace-nowrap">
                + Add New Teacher
            </a>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('admin.teachers.index') }}" class="mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <input type="text" name="search" placeholder="Search by name, email, mobile..." value="{{ request('search') }}" 
                       class="px-4 py-2 border rounded-lg">
                
                <select name="status" class="px-4 py-2 border rounded-lg">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Filter
                </button>
            </div>
        </form>

        <!-- Teachers Table -->
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
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($teachers as $teacher)
                                <tr>
                                    <td class="px-3 lg:px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 lg:h-10 lg:w-10">
                                                <img class="h-8 w-8 lg:h-10 lg:w-10 rounded-full" 
                                                     src="{{ $teacher->getProfileImageUrl() }}" 
                                                     alt="{{ $teacher->name }}">
                                            </div>
                                            <div class="ml-2 lg:ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $teacher->name }}</div>
                                                <div class="text-xs text-gray-500 sm:hidden">{{ \Str::limit($teacher->email, 25) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">{{ $teacher->email }}</td>
                                    <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">{{ $teacher->mobile ?? 'N/A' }}</td>
                                    <td class="px-3 lg:px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $teacher->status == 'active' ? 'bg-green-100 text-green-800' : ($teacher->status == 'suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($teacher->status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 lg:px-6 py-4 text-sm font-medium">
                                        <div class="flex flex-col sm:flex-row gap-1 sm:gap-0">
                                            <a href="{{ route('admin.teachers.show', $teacher->id) }}" 
                                               class="text-blue-600 hover:text-blue-900 sm:mr-3">View</a>
                                            <a href="{{ route('admin.teachers.edit', $teacher->id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 sm:mr-3">Edit</a>
                                            <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this teacher?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No teachers found
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
            {{ $teachers->appends(request()->except('page'))->links() }}
        </div>
    </div>
</div>
@endsection
