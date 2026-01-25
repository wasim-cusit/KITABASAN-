<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = StudentSetting::firstOrCreate(
            ['user_id' => auth()->id()],
            ['email_enrollment_confirmation' => true, 'email_course_updates' => true]
        );

        return view('student.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = StudentSetting::firstOrCreate(
            ['user_id' => auth()->id()],
            ['email_enrollment_confirmation' => true, 'email_course_updates' => true]
        );

        $settings->email_enrollment_confirmation = $request->boolean('email_enrollment_confirmation');
        $settings->email_course_updates = $request->boolean('email_course_updates');
        $settings->save();

        return redirect()->route('student.settings.index')->with('success', 'Settings updated successfully.');
    }
}
