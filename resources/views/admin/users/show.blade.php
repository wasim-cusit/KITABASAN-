@extends('layouts.admin')

@section('title', 'User Details')
@section('page-title', 'User Details')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- User Info -->
    <div class="lg:col-span-3 bg-white rounded-lg shadow p-6">
        <div class="flex items-center space-x-4 mb-6">
            <x-user-avatar :user="$user" size="xl" class="!h-20 !w-20 !text-xl" />
            <div>
                <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                <p class="text-gray-600">{{ $user->email }}</p>
                <p class="text-gray-600">{{ $user->mobile ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <span class="text-sm text-gray-500">Role</span>
                <p class="font-semibold">
                    @foreach($user->roles as $role)
                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">{{ ucfirst($role->name) }}</span>
                    @endforeach
                </p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Status</span>
                <p class="font-semibold">
                    <span class="px-2 py-1 text-xs rounded {{ $user->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Joined</span>
                <p class="font-semibold">{{ $user->created_at->format('M d, Y') }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Last Login</span>
                <p class="font-semibold">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}</p>
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Edit Admin
            </a>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                Back to Admins
            </a>
        </div>
    </div>
</div>
@endsection

