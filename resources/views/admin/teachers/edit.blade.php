@extends('layouts.admin')

@section('title', 'Edit Teacher')
@section('page-title', 'Edit Teacher')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('admin.teachers.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                           class="w-full px-4 py-2 border rounded-lg @error('first_name') border-red-500 @enderror"
                           placeholder="Enter first name">
                    @error('first_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                           class="w-full px-4 py-2 border rounded-lg @error('last_name') border-red-500 @enderror"
                           placeholder="Enter last name">
                    @error('last_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-4 py-2 border rounded-lg @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mobile</label>
                <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}"
                       class="w-full px-4 py-2 border rounded-lg @error('mobile') border-red-500 @enderror">
                @error('mobile')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password (leave blank to keep current)</label>
                <input type="password" name="password"
                       class="w-full px-4 py-2 border rounded-lg @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full px-4 py-2 border rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" required class="w-full px-4 py-2 border rounded-lg">
                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Update Teacher
                </button>
                <a href="{{ route('admin.teachers.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 text-center">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
