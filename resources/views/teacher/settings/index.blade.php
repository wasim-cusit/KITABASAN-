@extends('layouts.teacher')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="teacher-settings-container">
    <div class="teacher-settings-card">
        <h1 class="teacher-settings-title">Teacher Settings</h1>

        @if(session('success'))
            <div class="teacher-settings-success" style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:0.75rem 1rem;margin-bottom:1rem;">{{ session('success') }}</div>
        @endif

        <form action="{{ route('teacher.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="teacher-settings-sections">
                <div class="teacher-settings-section">
                    <h2 class="teacher-settings-section-title">Notification Settings</h2>
                    <div class="teacher-settings-options">
                        <label class="teacher-settings-option">
                            <input type="checkbox" name="email_notifications" value="1" @checked($settings['email_notifications'] ?? true)>
                            <span>Email notifications for new enrollments</span>
                        </label>
                        <label class="teacher-settings-option">
                            <input type="checkbox" name="course_updates" value="1" @checked($settings['course_updates'] ?? true)>
                            <span>Course update notifications</span>
                        </label>
                    </div>
                </div>

                <div class="teacher-settings-section">
                    <h2 class="teacher-settings-section-title">Privacy Settings</h2>
                    <div class="teacher-settings-options">
                        <label class="teacher-settings-option">
                            <input type="checkbox" name="show_profile" value="1" @checked($settings['show_profile'] ?? true)>
                            <span>Show my profile to students</span>
                        </label>
                        <label class="teacher-settings-option">
                            <input type="checkbox" name="show_email" value="1" @checked($settings['show_email'] ?? false)>
                            <span>Show email address</span>
                        </label>
                    </div>
                </div>

                <div class="teacher-settings-section">
                    <h2 class="teacher-settings-section-title">Device Management</h2>
                    <div class="teacher-settings-device-block">
                        <p class="teacher-settings-device-desc">
                            You can only access your account from one device at a time. If you need to use a different device, you can request a device reset.
                        </p>
                        <a href="{{ route('teacher.devices.index') }}" class="teacher-settings-device-link">Manage My Devices</a>
                    </div>
                </div>
            </div>

            <div class="teacher-settings-submit-wrap">
                <button type="submit" class="teacher-settings-submit-btn">Save Settings</button>
            </div>
        </form>
    </div>
</div>
@endsection
