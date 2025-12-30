<?php

namespace App\Http\Middleware;

use App\Models\DeviceBinding as DeviceBindingModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DeviceBinding
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Skip device binding check for admin and teacher roles
        // Only apply device limitation to students
        if ($user->hasRole('admin') || $user->hasRole('teacher')) {
            return $next($request);
        }

        $deviceFingerprint = $this->generateDeviceFingerprint($request);

        // Check if device is already bound
        $deviceBinding = DeviceBindingModel::where('user_id', $user->id)
            ->where('device_fingerprint', $deviceFingerprint)
            ->first();

        if (!$deviceBinding) {
            // Check if user already has an active device
            $activeDevice = DeviceBindingModel::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($activeDevice) {
                // Allow first device automatically, block others
                if ($activeDevice->device_fingerprint !== $deviceFingerprint) {
                    // Block access - user needs device reset
                    Auth::logout();
                    return redirect()->route('login')
                        ->with('error', 'You can only access from one device at a time. Please request a device reset from your account settings or contact admin.');
                }
            } else {
                // Check if there's a pending reset request
                $pendingReset = DeviceBindingModel::where('user_id', $user->id)
                    ->where('status', 'pending_reset')
                    ->first();

                if ($pendingReset) {
                    // Block access until admin approves reset
                    Auth::logout();
                    return redirect()->route('login')
                        ->with('info', 'Your device reset request is pending admin approval. Please wait for approval before logging in from a new device.');
                }

                // First device - auto bind
                DeviceBindingModel::create([
                    'user_id' => $user->id,
                    'device_fingerprint' => $deviceFingerprint,
                    'device_name' => $this->getDeviceName($request),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'status' => 'active',
                    'last_used_at' => now(),
                ]);
            }
        } else {
            // Update last used time
            if ($deviceBinding->status === 'active') {
                $deviceBinding->update([
                    'last_used_at' => now(),
                    'ip_address' => $request->ip(),
                ]);
            } elseif ($deviceBinding->status === 'pending_reset') {
                // Allow access but show message
                $deviceBinding->update([
                    'last_used_at' => now(),
                ]);
                // Don't block, but user should know reset is pending
            } else {
                // Blocked device
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Your device has been blocked. Please contact admin.');
            }
        }

        return $next($request);
    }

    /**
     * Generate device fingerprint
     */
    private function generateDeviceFingerprint(Request $request): string
    {
        $data = [
            $request->userAgent(),
            $request->ip(),
            $request->header('Accept-Language'),
            $request->header('Accept-Encoding'),
        ];

        return hash('sha256', implode('|', $data));
    }

    /**
     * Get device name from user agent
     */
    private function getDeviceName(Request $request): string
    {
        $userAgent = $request->userAgent();

        // Simple device name extraction
        if (strpos($userAgent, 'Windows') !== false) {
            return 'Windows Device';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            return 'Mac Device';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            return 'Linux Device';
        } elseif (strpos($userAgent, 'Android') !== false) {
            return 'Android Device';
        } elseif (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            return 'iOS Device';
        }

        return 'Unknown Device';
    }
}
