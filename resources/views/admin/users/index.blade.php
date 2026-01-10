@extends('layouts.admin')

@section('title', 'Users Management')
@section('page-title', 'Users Management')

@section('content')
<div class="bg-white rounded-lg shadow">
    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200">
        <nav class="flex -mb-px" id="usersTabs" role="tablist">
            <a href="{{ route('admin.users.index', ['tab' => 'all']) }}" 
               class="px-6 py-4 text-sm font-medium border-b-2 {{ ($activeTab ?? 'all') == 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                All Users
                <span class="ml-2 px-2 py-0.5 text-xs bg-gray-100 rounded-full">{{ $stats['all'] ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.users.index', ['tab' => 'teachers']) }}" 
               class="px-6 py-4 text-sm font-medium border-b-2 {{ ($activeTab ?? 'all') == 'teachers' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Teachers
                <span class="ml-2 px-2 py-0.5 text-xs bg-gray-100 rounded-full">{{ $stats['teachers'] ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.users.index', ['tab' => 'admins']) }}" 
               class="px-6 py-4 text-sm font-medium border-b-2 {{ ($activeTab ?? 'all') == 'admins' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Admins
                <span class="ml-2 px-2 py-0.5 text-xs bg-gray-100 rounded-full">{{ $stats['admins'] ?? 0 }}</span>
            </a>
        </nav>
    </div>

    <div class="p-4 lg:p-6">
        <!-- Header and Action Button -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-bold capitalize">
                    @if(($activeTab ?? 'all') == 'all')
                        All Users (Teachers & Admins)
                    @elseif(($activeTab ?? 'all') == 'teachers')
                        Teachers Management
                    @elseif(($activeTab ?? 'all') == 'admins')
                        Admins Management
                    @endif
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    @if(($activeTab ?? 'all') == 'teachers')
                        Active: {{ $stats['active_teachers'] ?? 0 }} | Total: {{ $stats['teachers'] ?? 0 }}
                    @elseif(($activeTab ?? 'all') == 'admins')
                        Active: {{ $stats['active_admins'] ?? 0 }} | Total: {{ $stats['admins'] ?? 0 }}
                    @else
                        <span class="text-blue-600">Note: Students are managed in the dedicated <a href="{{ route('admin.students.index') }}" class="underline">Students section</a></span>
                    @endif
                </p>
            </div>
            <a href="{{ route('admin.users.create', ['tab' => $activeTab ?? 'all']) }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm lg:text-base text-center whitespace-nowrap">
                + Add New {{ ucfirst(($activeTab ?? 'all') == 'all' ? 'User' : rtrim(($activeTab ?? 'all'), 's')) }}
            </a>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6">
            <input type="hidden" name="tab" value="{{ $activeTab ?? 'all' }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <input type="text" name="search" placeholder="Search by name, email, mobile..." value="{{ request('search') }}" 
                       class="px-4 py-2 border rounded-lg">
                
                @if(($activeTab ?? 'all') == 'all')
                    <select name="role" class="px-4 py-2 border rounded-lg">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                @endif
                
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

        <!-- Users Table -->
        <div class="overflow-x-auto -mx-4 lg:mx-0">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Email</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Mobile</th>
                                @if(($activeTab ?? 'all') == 'all')
                                    <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                @endif
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $user)
                                <tr>
                                    <td class="px-3 lg:px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 lg:h-10 lg:w-10">
                                                <img class="h-8 w-8 lg:h-10 lg:w-10 rounded-full" 
                                                     src="{{ $user->profile_image ? \Storage::url($user->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" 
                                                     alt="{{ $user->name }}">
                                            </div>
                                            <div class="ml-2 lg:ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500 sm:hidden">{{ \Str::limit($user->email, 25) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">{{ $user->email }}</td>
                                    <td class="px-3 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">{{ $user->mobile ?? 'N/A' }}</td>
                                    @if(($activeTab ?? 'all') == 'all')
                                        <td class="px-3 lg:px-6 py-4">
                                            @foreach($user->roles as $role)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ ucfirst($role->name) }}
                                                </span>
                                            @endforeach
                                        </td>
                                    @endif
                                    <td class="px-3 lg:px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $user->status == 'active' ? 'bg-green-100 text-green-800' : ($user->status == 'suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 lg:px-6 py-4 text-sm font-medium">
                                        <div class="flex flex-col sm:flex-row gap-1 sm:gap-0">
                                            <a href="{{ route('admin.users.show', ['user' => $user->id, 'tab' => $activeTab ?? 'all']) }}" 
                                               class="text-blue-600 hover:text-blue-900 sm:mr-3">View</a>
                                            <a href="{{ route('admin.users.edit', ['user' => $user->id, 'tab' => $activeTab ?? 'all']) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 sm:mr-3">Edit</a>
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="tab" value="{{ $activeTab ?? 'all' }}">
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ ($activeTab ?? 'all') == 'all' ? '6' : '5' }}" 
                                        class="px-6 py-4 text-center text-gray-500">
                                        @if(($activeTab ?? 'all') == 'teachers')
                                            No teachers found
                                        @elseif(($activeTab ?? 'all') == 'admins')
                                            No admins found
                                        @else
                                            No users found
                                        @endif
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
            {{ $users->appends(request()->except('page'))->links() }}
        </div>
    </div>
</div>
@endsection
