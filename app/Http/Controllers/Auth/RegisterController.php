<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AccountCreatedMail;
use App\Models\User;
use App\Services\AdminNotificationService;
use App\Services\EmailConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile' => 'required|string|max:20|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        // Assign student role by default
        $user->assignRole('student');

        try {
            EmailConfigService::apply();
            if (EmailConfigService::isConfigured()) {
                Mail::to($user->email)->send(new AccountCreatedMail($user, 'student', null));
            }
        } catch (\Throwable $e) {
            Log::warning('AccountCreatedMail not sent after registration: ' . $e->getMessage());
        }

        AdminNotificationService::notifyNewStudent($user, 'registration');

        Auth::login($user);

        return redirect()->route('student.dashboard');
    }
}
