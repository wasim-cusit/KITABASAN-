<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceBinding;
use App\Models\User;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $query = DeviceBinding::with('user');

        // Search
        if ($request->has('search') && $request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Show pending reset requests first
        if (!$request->has('status')) {
            $query->orderByRaw("CASE WHEN status = 'pending_reset' THEN 0 ELSE 1 END");
        }

        $devices = $query->latest()->paginate(20);
        $users = User::role('student')->orWhereHas('roles', function($q) {
            $q->where('name', 'teacher');
        })->get();

        // Count pending reset requests
        $pendingResetCount = DeviceBinding::where('status', 'pending_reset')->count();

        return view('admin.devices.index', compact('devices', 'users', 'pendingResetCount'));
    }

    public function resetDevice($id)
    {
        $device = DeviceBinding::findOrFail($id);
        $user = $device->user;

        // Delete all device bindings for this user to allow fresh login
        DeviceBinding::where('user_id', $user->id)->delete();

        return redirect()->back()
            ->with('success', 'All device bindings for this user have been reset. User can now login from a new device.');
    }

    public function approveReset($id)
    {
        $device = DeviceBinding::findOrFail($id);
        $user = $device->user;

        // Delete all device bindings for this user
        DeviceBinding::where('user_id', $user->id)->delete();

        return redirect()->back()
            ->with('success', 'Device reset approved. User can now login from a new device.');
    }

    public function rejectReset($id)
    {
        $device = DeviceBinding::findOrFail($id);

        // Reject the reset request and set device back to active
        $device->update([
            'status' => 'active',
            'reset_requested_at' => null,
            'reset_request_reason' => null,
        ]);

        return redirect()->back()
            ->with('success', 'Device reset request rejected. Device remains active.');
    }

    public function blockDevice($id)
    {
        $device = DeviceBinding::findOrFail($id);
        $device->update(['status' => 'blocked']);

        return redirect()->back()
            ->with('success', 'Device blocked successfully.');
    }

    public function unblockDevice($id)
    {
        $device = DeviceBinding::findOrFail($id);
        $device->update(['status' => 'active']);

        return redirect()->back()
            ->with('success', 'Device unblocked successfully.');
    }

    public function resetUserDevices($userId)
    {
        $user = User::findOrFail($userId);
        DeviceBinding::where('user_id', $userId)->delete();

        return redirect()->back()
            ->with('success', 'All device bindings for this user have been reset.');
    }
}
