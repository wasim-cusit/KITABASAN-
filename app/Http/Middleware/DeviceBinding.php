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

        $user = Auth::user();
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
                        ->with('error', 'You can only access from one device. Please contact admin for device reset.');
                }
            } else {
                // First device - auto bind
                DeviceBindingModel::create([
                    'user_id' => $user->id,
                    'device_fingerprint' => $deviceFingerprint,
                    'device_name' => $request->userAgent(),
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
            } else {
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
}
