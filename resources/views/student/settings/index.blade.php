@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Student Settings</h1>

        <form action="{{ route('student.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold mb-4">Notification Settings</h2>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="email_notifications" value="1" checked class="mr-2">
                            <span>Email notifications for course updates</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="course_reminders" value="1" checked class="mr-2">
                            <span>Course completion reminders</span>
                        </label>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold mb-4">Learning Preferences</h2>
                    <div class="space-y-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Default Video Playback Speed</label>
                            <select name="video_speed" class="w-full px-3 py-2 border rounded">
                                <option value="0.5">0.5x</option>
                                <option value="0.75">0.75x</option>
                                <option value="1" selected>1x (Normal)</option>
                                <option value="1.25">1.25x</option>
                                <option value="1.5">1.5x</option>
                                <option value="2">2x</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold mb-4">Device Management</h2>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-gray-700 mb-3">
                            You can only access your account from one device at a time. If you need to use a different device, you can request a device reset.
                        </p>
                        <a href="{{ route('student.devices.index') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
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


