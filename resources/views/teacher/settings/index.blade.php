@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Teacher Settings</h1>

        <form action="{{ route('teacher.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold mb-4">Notification Settings</h2>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="email_notifications" value="1" checked class="mr-2">
                            <span>Email notifications for new enrollments</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="course_updates" value="1" checked class="mr-2">
                            <span>Course update notifications</span>
                        </label>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold mb-4">Privacy Settings</h2>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="show_profile" value="1" checked class="mr-2">
                            <span>Show my profile to students</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="show_email" value="1" class="mr-2">
                            <span>Show email address</span>
                        </label>
                    </div>
                </div>
            </div>

                <div>
                    <h2 class="text-xl font-semibold mb-4">Device Management</h2>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-gray-700 mb-3">
                            You can only access your account from one device at a time. If you need to use a different device, you can request a device reset.
                        </p>
                        <a href="{{ route('teacher.devices.index') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Manage My Devices
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


