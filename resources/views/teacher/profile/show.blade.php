@extends('layouts.teacher')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    <div class="bg-white rounded-lg shadow p-4 lg:p-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4">
            <h1 class="text-xl lg:text-2xl font-bold">My Profile</h1>
            <a href="{{ route('teacher.profile.edit') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm lg:text-base text-center">
                Edit Profile
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <p class="text-gray-900">{{ $user->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <p class="text-gray-900">{{ $user->email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mobile</label>
                <p class="text-gray-900">{{ $user->mobile }}</p>
            </div>
            @if($profile)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total Courses</label>
                <p class="text-gray-900">{{ $profile->total_courses }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total Students</label>
                <p class="text-gray-900">{{ $profile->total_students }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                <p class="text-gray-900">{{ number_format($profile->rating, 2) }} / 5.00</p>
            </div>
            @endif
            @if($user->bio || ($profile && $profile->bio))
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                <p class="text-gray-900">{{ $profile->bio ?? $user->bio }}</p>
            </div>
            @endif
            @if($profile && $profile->qualifications)
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Qualifications</label>
                <p class="text-gray-900">{{ $profile->qualifications }}</p>
            </div>
            @endif
        </div>

        <div class="mt-6 pt-6 border-t">
            <h2 class="text-xl font-bold mb-4">Change Password</h2>
            <form action="{{ route('teacher.profile.password.update') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input type="password" name="current_password" required class="w-full px-3 py-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" name="password" required class="w-full px-3 py-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border rounded">
                    </div>
                </div>
                <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

