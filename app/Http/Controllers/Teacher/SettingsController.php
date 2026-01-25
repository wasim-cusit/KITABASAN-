<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $profile = TeacherProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'email_notifications' => true,
                'course_updates' => true,
                'show_profile' => true,
                'show_email' => false,
            ]
        );

        $settings = [
            'email_notifications' => (bool) ($profile->email_notifications ?? true),
            'course_updates' => (bool) ($profile->course_updates ?? true),
            'show_profile' => (bool) ($profile->show_profile ?? true),
            'show_email' => (bool) ($profile->show_email ?? false),
        ];

        return view('teacher.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $profile = TeacherProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'email_notifications' => true,
                'course_updates' => true,
                'show_profile' => true,
                'show_email' => false,
            ]
        );

        $profile->email_notifications = $request->boolean('email_notifications');
        $profile->course_updates = $request->boolean('course_updates');
        $profile->show_profile = $request->boolean('show_profile');
        $profile->show_email = $request->boolean('show_email');
        $profile->save();

        return redirect()->route('teacher.settings.index')->with('success', 'Settings saved successfully.');
    }
}
