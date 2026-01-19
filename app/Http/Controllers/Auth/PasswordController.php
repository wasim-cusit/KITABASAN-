<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Models\SystemSetting;
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

        // Apply email configuration from system settings
        $this->applyEmailConfiguration();

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

    /**
     * Apply email configuration from system settings
     */
    private function applyEmailConfiguration(): void
    {
        try {
            // Clear system settings cache to get fresh values
            \App\Models\SystemSetting::clearCache();
            
            // Clear mail config cache
            if (app()->bound('mail.manager')) {
                app()['mail.manager']->forgetMailers();
            }

            // Get email settings from SystemSetting (bypass cache)
            $mailDriver = \App\Models\SystemSetting::where('key', 'mail_driver')->where('is_active', true)->first();
            $mailDriver = $mailDriver ? $mailDriver->value : config('mail.default', 'log');
            
            $mailHost = \App\Models\SystemSetting::where('key', 'mail_host')->where('is_active', true)->first();
            $mailHost = $mailHost ? $mailHost->value : config('mail.mailers.smtp.host');
            
            $mailPort = \App\Models\SystemSetting::where('key', 'mail_port')->where('is_active', true)->first();
            $mailPort = $mailPort ? (int)$mailPort->value : config('mail.mailers.smtp.port');
            
            $mailUsername = \App\Models\SystemSetting::where('key', 'mail_username')->where('is_active', true)->first();
            $mailUsername = $mailUsername ? $mailUsername->value : config('mail.mailers.smtp.username');
            
            $mailPassword = \App\Models\SystemSetting::where('key', 'mail_password')->where('is_active', true)->first();
            $mailPassword = $mailPassword ? $mailPassword->value : config('mail.mailers.smtp.password');
            
            $mailEncryption = \App\Models\SystemSetting::where('key', 'mail_encryption')->where('is_active', true)->first();
            $mailEncryption = $mailEncryption ? $mailEncryption->value : 'tls';
            
            $mailFromAddress = \App\Models\SystemSetting::where('key', 'mail_from_address')->where('is_active', true)->first();
            $mailFromAddress = $mailFromAddress ? $mailFromAddress->value : (SystemSetting::getValue('site_email') ?: config('mail.from.address'));
            
            $mailFromName = \App\Models\SystemSetting::where('key', 'mail_from_name')->where('is_active', true)->first();
            $mailFromName = $mailFromName ? $mailFromName->value : (SystemSetting::getValue('site_name') ?: config('mail.from.name'));

            // Apply mail driver (must be set)
            Config::set('mail.default', $mailDriver ?: 'log');

            // Apply SMTP configuration
            Config::set('mail.mailers.smtp.host', $mailHost ?: '127.0.0.1');
            Config::set('mail.mailers.smtp.port', $mailPort ?: 2525);
            Config::set('mail.mailers.smtp.username', $mailUsername);
            Config::set('mail.mailers.smtp.password', $mailPassword);
            Config::set('mail.mailers.smtp.encryption', $mailEncryption ?: 'tls');

            // Apply from address and name
            Config::set('mail.from.address', $mailFromAddress ?: config('mail.from.address', 'noreply@example.com'));
            Config::set('mail.from.name', $mailFromName ?: config('mail.from.name', config('app.name', 'KITAB ASAN')));

            Log::info('Email configuration applied for password reset', [
                'driver' => $mailDriver,
                'host' => $mailHost,
                'port' => $mailPort,
                'username' => $mailUsername ? 'set' : 'not set',
                'encryption' => $mailEncryption,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to apply email configuration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
