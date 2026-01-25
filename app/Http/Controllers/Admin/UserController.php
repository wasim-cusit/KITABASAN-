<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountCreatedMail;
use App\Models\User;
use App\Services\EmailConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('admin'); // Only show admins

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('mobile', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $users = $query->with('roles')->latest()->paginate(15);

        // Get statistics
        $stats = [
            'total' => User::role('admin')->count(),
            'active' => User::role('admin')->where('status', 'active')->count(),
            'inactive' => User::role('admin')->where('status', 'inactive')->count(),
            'suspended' => User::role('admin')->where('status', 'suspended')->count(),
        ];

        // Add pagination
        $users->appends($request->except('page'));

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'nullable|string|unique:users,mobile',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'status' => $request->status,
            'email_verified_at' => now(),
        ]);

        $user->assignRole('admin');

        try {
            EmailConfigService::apply();
            if (EmailConfigService::isConfigured()) {
                Mail::to($user->email)->send(new AccountCreatedMail($user, 'admin', 'admin'));
            }
        } catch (\Throwable $e) {
            Log::warning('AccountCreatedMail not sent (admin): ' . $e->getMessage());
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin created successfully.');
    }

    public function show(User $user)
    {
        // Ensure this is an admin
        if (!$user->hasRole('admin')) {
            abort(404, 'User is not an admin.');
        }

        $user->load(['roles', 'enrollments.book', 'payments', 'deviceBindings']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Ensure this is an admin
        if (!$user->hasRole('admin')) {
            abort(404, 'User is not an admin.');
        }

        $user->load('roles');
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Ensure this is an admin
        if (!$user->hasRole('admin')) {
            abort(404, 'User is not an admin.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|unique:users,mobile,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
            'mobile' => $request->mobile,
            'status' => $request->status,
        ];

        // Prevent deactivating the super admin (would lock them out)
        if ($user->isSuperAdmin() && $request->status !== 'active') {
            return redirect()->route('admin.users.edit', $user)
                ->with('error', 'The super admin account cannot be deactivated.');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin updated successfully.');
    }

    public function destroy(User $user)
    {
        // Ensure this is an admin
        if (!$user->hasRole('admin')) {
            abort(404, 'User is not an admin.');
        }

        // Prevent deleting the super admin (admin@kitabasan.com)
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'The super admin account cannot be deleted.');
        }

        // Prevent deleting the last admin user
        if (User::role('admin')->count() <= 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete the last admin user.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin deleted successfully.');
    }
}
