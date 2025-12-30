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

        $devices = $query->latest()->paginate(20);
        $users = User::role('student')->get();

        return view('admin.devices.index', compact('devices', 'users'));
    }

    public function resetDevice($id)
    {
        $device = DeviceBinding::findOrFail($id);

        // Delete the device binding to allow user to login from new device
        $device->delete();

        return redirect()->back()
            ->with('success', 'Device binding reset successfully. User can now login from a new device.');
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
