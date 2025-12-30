<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\DeviceBinding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = DeviceBinding::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('teacher.devices.index', compact('devices'));
    }

    public function requestReset(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $user = Auth::user();

        // Get the current active device
        $activeDevice = DeviceBinding::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$activeDevice) {
            return redirect()->back()
                ->with('error', 'No active device found.');
        }

        // Check if there's already a pending reset request
        if ($activeDevice->status === 'pending_reset') {
            return redirect()->back()
                ->with('info', 'You already have a pending device reset request. Please wait for admin approval.');
        }

        // Update device status to pending_reset
        $activeDevice->update([
            'status' => 'pending_reset',
            'reset_requested_at' => now(),
            'reset_request_reason' => $request->reason,
        ]);

        return redirect()->back()
            ->with('success', 'Device reset request submitted successfully. Please wait for admin approval.');
    }
}

