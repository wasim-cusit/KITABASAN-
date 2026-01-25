@extends('layouts.teacher')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="teacher-profile-container">
<div class="teacher-profile-card">
    <div class="teacher-profile-header">
        <div class="teacher-profile-hero">
            @if($user->profile_image)
                <img src="{{ \Storage::url($user->profile_image) }}" alt="{{ $user->name }}" class="teacher-profile-avatar">
            @else
                <div class="teacher-profile-avatar-placeholder">{{ $user->getInitials() }}</div>
            @endif
            <div>
                <h1 class="teacher-profile-name">
                    {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->name }}
                </h1>
                <p class="teacher-profile-email">{{ $user->email }}</p>
            </div>
        </div>
        <a href="{{ route('teacher.profile.edit') }}" class="teacher-profile-edit-btn">Edit Profile</a>
    </div>

    <!-- Basic Information -->
    <div class="teacher-profile-section">
        <h2 class="teacher-profile-section-title">Basic Information</h2>
        <div class="teacher-profile-grid">
            <div class="teacher-profile-field">
                <label class="teacher-profile-label">First Name</label>
                <p class="teacher-profile-value">{{ $user->first_name ?? 'N/A' }}</p>
            </div>
            <div class="teacher-profile-field">
                <label class="teacher-profile-label">Last Name</label>
                <p class="teacher-profile-value">{{ $user->last_name ?? 'N/A' }}</p>
            </div>
            <div class="teacher-profile-field">
                <label class="teacher-profile-label">Email</label>
                <p class="teacher-profile-value">{{ $user->email }}</p>
            </div>
            <div class="teacher-profile-field">
                <label class="teacher-profile-label">Mobile</label>
                <p class="teacher-profile-value">{{ $user->mobile ?? 'N/A' }}</p>
            </div>
            @if($user->date_of_birth)
            <div class="teacher-profile-field">
                <label class="teacher-profile-label">Date of Birth</label>
                <p class="teacher-profile-value">{{ $user->date_of_birth->format('F d, Y') }}</p>
            </div>
            @endif
            @if($profile)
            <div class="teacher-profile-field">
                <label class="teacher-profile-label">Total Courses</label>
                <p class="teacher-profile-value">{{ $profile->total_courses ?? 0 }}</p>
            </div>
            <div class="teacher-profile-field">
                <label class="teacher-profile-label">Total Students</label>
                <p class="teacher-profile-value">{{ $profile->total_students ?? 0 }}</p>
            </div>
            <div class="teacher-profile-field">
                <label class="teacher-profile-label">Rating</label>
                <p class="teacher-profile-value">{{ number_format($profile->rating ?? 0, 2) }} / 5.00</p>
            </div>
            @endif
            @if($user->bio || ($profile && $profile->bio))
            <div class="teacher-profile-field teacher-profile-field--full">
                <label class="teacher-profile-label">Bio</label>
                <p class="teacher-profile-value teacher-profile-value--pre">{{ $profile->bio ?? $user->bio }}</p>
            </div>
            @endif
            @if($profile && $profile->qualifications)
            <div class="teacher-profile-field teacher-profile-field--full">
                <label class="teacher-profile-label">Qualifications</label>
                <p class="teacher-profile-value teacher-profile-value--pre">{{ $profile->qualifications }}</p>
            </div>
            @endif
            @if($profile && $profile->specializations)
            <div class="teacher-profile-field teacher-profile-field--full">
                <label class="teacher-profile-label">Specializations</label>
                <p class="teacher-profile-value">{{ $profile->specializations }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Address Information -->
    @if($user->address || $user->city || $user->state || $user->country || $user->postal_code)
    <div class="teacher-profile-section">
        <h2 class="teacher-profile-section-title">Address Information</h2>
        <div class="teacher-profile-grid">
            @if($user->address)
            <div class="teacher-profile-field teacher-profile-field--full">
                <label class="teacher-profile-label">Address</label>
                <p class="teacher-profile-value">{{ $user->address }}</p>
            </div>
            @endif
            @if($user->city)
            <div class="teacher-profile-field">
                <label class="teacher-profile-label">City</label>
                <p class="teacher-profile-value">{{ $user->city }}</p>
            </div>
            @endif
            @if($user->state)
            <div class="teacher-profile-field">
                <label class="teacher-profile-label">State/Province</label>
                <p class="teacher-profile-value">{{ $user->state }}</p>
            </div>
            @endif
            @if($user->country)
            <div class="teacher-profile-field">
                <label class="teacher-profile-label">Country</label>
                <p class="teacher-profile-value">{{ $user->country }}</p>
            </div>
            @endif
            @if($user->postal_code)
            <div class="teacher-profile-field">
                <label class="teacher-profile-label">Postal Code</label>
                <p class="teacher-profile-value">{{ $user->postal_code }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Change Password Section -->
    <div class="teacher-profile-section">
        <h2 class="teacher-profile-section-title">Change Password</h2>
        <form action="{{ route('teacher.profile.password.update') }}" method="POST">
            @csrf
            <div class="teacher-profile-password-grid">
                <div>
                    <label class="teacher-profile-label">Current Password</label>
                    <input type="password" name="current_password" required class="teacher-profile-input @error('current_password') border-red-500 @enderror">
                    @error('current_password')
                        <p class="teacher-profile-error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="teacher-profile-label">New Password</label>
                    <input type="password" name="password" required class="teacher-profile-input @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="teacher-profile-error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="teacher-profile-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="teacher-profile-input">
                </div>
            </div>
            <button type="submit" class="teacher-profile-submit-btn">Update Password</button>
        </form>
    </div>
</div>
</div>
@endsection
