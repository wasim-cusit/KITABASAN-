<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EmailConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        EmailConfigService::apply();

        // Check if mail is properly configured
        $mailDriver = config('mail.default', 'log');
        $mailHost = config('mail.mailers.smtp.host');
        
        if ($mailDriver === 'log') {
            Log::warning('Password reset attempted but mail driver is set to "log"', [
                'email' => $request->email,
            ]);
            
            return back()->withErrors([
                'email' => 'Email sending is not configured. Please contact the administrator to set up email settings.'
            ]);
        }

        if ($mailDriver === 'smtp' && empty($mailHost)) {
            Log::warning('Password reset attempted but SMTP host is not configured', [
                'email' => $request->email,
            ]);
            
            return back()->withErrors([
                'email' => 'Email configuration is incomplete. Please contact the administrator.'
            ]);
        }

        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                Log::info('Password reset link sent', [
                    'email' => $request->email,
                    'mail_driver' => $mailDriver,
                    'mail_host' => $mailHost,
                ]);
                
                return back()->with([
                    'status' => 'We have emailed your password reset link. Please check your inbox (and spam folder).'
                ]);
            } else {
                Log::warning('Password reset link failed to send', [
                    'email' => $request->email,
                    'status' => $status,
                    'mail_driver' => $mailDriver,
                ]);
                
                return back()->withErrors(['email' => __($status)]);
            }
        } catch (\Exception $e) {
            Log::error('Password reset email error', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'mail_driver' => $mailDriver,
            ]);

            return back()->withErrors([
                'email' => 'Unable to send password reset email. Error: ' . $e->getMessage() . '. Please check your email configuration or contact support.'
            ]);
        }
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
