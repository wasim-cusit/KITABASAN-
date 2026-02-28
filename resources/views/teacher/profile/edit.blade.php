@extends('layouts.teacher')

@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')

@section('content')
<div class="bg-white rounded-lg shadow p-4 lg:p-6">
    <h1 class="text-xl lg:text-2xl font-bold mb-6">Edit Profile</h1>

    <form action="{{ route('teacher.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Profile Picture Section -->
        <div class="mb-6 pb-6 border-b">
            <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
            <div class="flex items-center space-x-4">
                <div id="profilePreview" class="flex-shrink-0">
                    <x-user-avatar :user="$user" size="xl" class="border-4 border-blue-100" />
                </div>
                <div class="flex-1">
                    <input type="file" name="profile_image" id="profile_image" accept="image/*"
                           class="w-full px-3 py-2 border rounded-lg @error('profile_image') border-red-500 @enderror"
                           onchange="previewProfileImage(this)">
                    <p class="text-xs text-gray-500 mt-1">Maximum file size: 5MB. JPG, PNG, GIF allowed.</p>
                    @error('profile_image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="mb-6">
            <h2 class="text-xl font-bold mb-4">Basic Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                           class="w-full px-3 py-2 border rounded-lg @error('first_name') border-red-500 @enderror"
                           placeholder="Enter first name">
                    @error('first_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                           class="w-full px-3 py-2 border rounded-lg @error('last_name') border-red-500 @enderror"
                           placeholder="Enter last name">
                    @error('last_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3 py-2 border rounded-lg @error('email') border-red-500 @enderror">
                    @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile</label>
                    <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}"
                           class="w-full px-3 py-2 border rounded-lg @error('mobile') border-red-500 @enderror"
                           placeholder="Enter mobile number">
                    @error('mobile')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border rounded-lg @error('date_of_birth') border-red-500 @enderror"
                           max="{{ date('Y-m-d') }}">
                    @error('date_of_birth')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                    <textarea name="bio" rows="4"
                              class="w-full px-3 py-2 border rounded-lg @error('bio') border-red-500 @enderror"
                              placeholder="Tell us about yourself...">{{ old('bio', $profile->bio ?? $user->bio) }}</textarea>
                    @error('bio')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Qualifications</label>
                    <textarea name="qualifications" rows="3"
                              class="w-full px-3 py-2 border rounded-lg @error('qualifications') border-red-500 @enderror"
                              placeholder="Enter your qualifications...">{{ old('qualifications', $profile->qualifications ?? '') }}</textarea>
                    @error('qualifications')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Specializations</label>
                    <input type="text" name="specializations" value="{{ old('specializations', $profile->specializations ?? '') }}"
                           class="w-full px-3 py-2 border rounded-lg @error('specializations') border-red-500 @enderror"
                           placeholder="e.g., Mathematics, Physics, Computer Science">
                    @error('specializations')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="mb-6">
            <h2 class="text-xl font-bold mb-4">Address Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                    <textarea name="address" rows="2"
                              class="w-full px-3 py-2 border rounded-lg @error('address') border-red-500 @enderror"
                              placeholder="Enter your street address">{{ old('address', $user->address) }}</textarea>
                    @error('address')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" name="city" value="{{ old('city', $user->city) }}"
                           class="w-full px-3 py-2 border rounded-lg @error('city') border-red-500 @enderror"
                           placeholder="Enter city">
                    @error('city')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State/Province</label>
                    <input type="text" name="state" value="{{ old('state', $user->state) }}"
                           class="w-full px-3 py-2 border rounded-lg @error('state') border-red-500 @enderror"
                           placeholder="Enter state or province">
                    @error('state')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <input type="text" name="country" value="{{ old('country', $user->country) }}"
                           class="w-full px-3 py-2 border rounded-lg @error('country') border-red-500 @enderror"
                           placeholder="Enter country">
                    @error('country')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}"
                           class="w-full px-3 py-2 border rounded-lg @error('postal_code') border-red-500 @enderror"
                           placeholder="Enter postal code">
                    @error('postal_code')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Cover Image -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
            @if($profile && $profile->hasValidCoverImage())
                <div id="coverPreview" class="mb-2">
                    <img src="{{ $profile->getCoverImageUrl() }}"
                         alt="Cover"
                         class="h-48 w-full object-cover rounded-lg border"
                         loading="lazy"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="hidden h-48 w-full bg-gradient-to-br from-blue-400 to-indigo-600 rounded-lg border flex items-center justify-center text-white font-semibold">
                        No cover image
                    </div>
                </div>
            @else
                <div id="coverPreview" class="h-48 w-full bg-gradient-to-br from-blue-400 to-indigo-600 rounded-lg mb-2 flex items-center justify-center text-white font-semibold border">
                    No cover image
                </div>
            @endif
            <input type="file" name="cover_image" id="cover_image" accept="image/*"
                   class="w-full px-3 py-2 border rounded-lg @error('cover_image') border-red-500 @enderror"
                   onchange="previewCoverImage(this)">
            <p class="text-xs text-gray-500 mt-1">Maximum file size: 5MB. JPG, PNG, GIF allowed.</p>
            @error('cover_image')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-4 pt-4 border-t">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Update Profile
            </button>
            <a href="{{ route('teacher.profile.show') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
function previewProfileImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('profilePreview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="h-24 w-24 rounded-full object-cover border-4 border-blue-100">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewCoverImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('coverPreview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="h-48 w-full object-cover rounded-lg border">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
