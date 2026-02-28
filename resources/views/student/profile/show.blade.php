@extends('layouts.student')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="bg-white rounded-lg shadow p-4 lg:p-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-6 gap-4">
        <div class="flex items-center space-x-4">
            <!-- Profile Picture with Initials Fallback -->
            <x-user-avatar :user="$user" size="xl" class="border-4 border-blue-100" />
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">
                    {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->name }}
                </h1>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
            </div>
        </div>
        <a href="{{ route('student.profile.edit') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm lg:text-base text-center">
            Edit Profile
        </a>
    </div>

    <!-- Basic Information -->
    <div class="border-t pt-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Basic Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                <p class="text-gray-900">{{ $user->first_name ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                <p class="text-gray-900">{{ $user->last_name ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <p class="text-gray-900">{{ $user->email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mobile</label>
                <p class="text-gray-900">{{ $user->mobile ?? 'N/A' }}</p>
            </div>
            @if($user->date_of_birth)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                <p class="text-gray-900">{{ $user->date_of_birth->format('F d, Y') }}</p>
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <span class="px-2 py-1 text-xs rounded-full {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($user->status) }}
                </span>
            </div>
            @if($user->bio)
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                <p class="text-gray-900 whitespace-pre-line">{{ $user->bio }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Address Information -->
    @if($user->address || $user->city || $user->state || $user->country || $user->postal_code)
    <div class="border-t pt-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Address Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($user->address)
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <p class="text-gray-900">{{ $user->address }}</p>
            </div>
            @endif
            @if($user->city)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                <p class="text-gray-900">{{ $user->city }}</p>
            </div>
            @endif
            @if($user->state)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">State/Province</label>
                <p class="text-gray-900">{{ $user->state }}</p>
            </div>
            @endif
            @if($user->country)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                <p class="text-gray-900">{{ $user->country }}</p>
            </div>
            @endif
            @if($user->postal_code)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                <p class="text-gray-900">{{ $user->postal_code }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Change Password Section -->
    <div class="border-t pt-6">
        <h2 class="text-xl font-bold mb-4">Change Password</h2>
        <form action="{{ route('student.profile.password.update') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input type="password" name="current_password" required
                           class="w-full px-3 py-2 border rounded-lg @error('current_password') border-red-500 @enderror">
                    @error('current_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password" required
                           class="w-full px-3 py-2 border rounded-lg @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>
            <button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Update Password
            </button>
        </form>
    </div>
</div>
@endsection
