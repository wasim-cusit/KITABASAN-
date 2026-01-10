<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $profile = $user->teacherProfile;
        return view('teacher.profile.show', compact('user', 'profile'));
    }

    public function edit()
    {
        $user = Auth::user();
        $profile = $user->teacherProfile;
        return view('teacher.profile.edit', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:20|unique:users,mobile,' . $user->id,
            'bio' => 'nullable|string|max:1000',
            'qualifications' => 'nullable|string|max:1000',
            'specializations' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|max:5120', // 5MB
            'cover_image' => 'nullable|image|max:5120',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
        ]);

        // Generate name from first_name and last_name for backwards compatibility
        $validated['name'] = trim($validated['first_name'] . ' ' . $validated['last_name']);

        // Handle profile image upload separately
        $userData = $validated;
        unset($userData['profile_image'], $userData['cover_image'], $userData['qualifications'], $userData['specializations']);
        
        if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $userData['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        $user->update($userData);

        // Update teacher profile
        $profile = $user->teacherProfile ?? new TeacherProfile();
        $profile->user_id = $user->id;
        $profile->bio = $request->bio;
        $profile->qualifications = $request->qualifications;
        $profile->specializations = $request->specializations;

        if ($request->hasFile('cover_image')) {
            // Delete old cover image if exists
            if ($profile->cover_image && Storage::disk('public')->exists($profile->cover_image)) {
                Storage::disk('public')->delete($profile->cover_image);
            }
            $profile->cover_image = $request->file('cover_image')->store('covers', 'public');
        }

        $profile->save();

        return redirect()->route('teacher.profile.show')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('teacher.profile.show')->with('success', 'Password updated successfully.');
    }
}
