<?php

namespace App\Services;

use App\Models\DeviceBinding;
use App\Models\User;
use Illuminate\Http\Request;

class DeviceService
{
    /**
     * Generate device fingerprint
     */
    public function generateDeviceFingerprint(Request $request): string
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
     * Bind device to user
     */
    public function bindDevice(User $user, Request $request): DeviceBinding
    {
        $fingerprint = $this->generateDeviceFingerprint($request);

        return DeviceBinding::updateOrCreate(
            [
                'user_id' => $user->id,
                'device_fingerprint' => $fingerprint,
            ],
            [
                'device_name' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'active',
                'last_used_at' => now(),
            ]
        );
    }

    /**
     * Check if device is allowed
     */
    public function isDeviceAllowed(User $user, string $fingerprint): bool
    {
        $device = DeviceBinding::where('user_id', $user->id)
            ->where('device_fingerprint', $fingerprint)
            ->first();

        return $device && $device->status === 'active';
    }

    /**
     * Request device reset
     */
    public function requestDeviceReset(User $user, int $deviceId): bool
    {
        $device = DeviceBinding::where('id', $deviceId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Auto-approve if only one device
        $activeDevices = DeviceBinding::where('user_id', $user->id)
            ->where('status', 'active')
            ->count();

        if ($activeDevices <= 1) {
            $device->update(['status' => 'active']);
            return true;
        }

        $device->update(['status' => 'reset_requested']);
        return true;
    }

    /**
     * Approve device reset (Admin only)
     */
    public function approveDeviceReset(int $deviceId): bool
    {
        $device = DeviceBinding::findOrFail($deviceId);

        // Block old device
        DeviceBinding::where('user_id', $device->user_id)
            ->where('id', '!=', $deviceId)
            ->where('status', 'active')
            ->update(['status' => 'blocked']);

        // Activate new device
        $device->update([
            'status' => 'active',
            'last_used_at' => now(),
        ]);

        return true;
    }

    /**
     * Get user's active devices
     */
    public function getUserDevices(User $user)
    {
        return DeviceBinding::where('user_id', $user->id)
            ->orderBy('last_used_at', 'desc')
            ->get();
    }
}

