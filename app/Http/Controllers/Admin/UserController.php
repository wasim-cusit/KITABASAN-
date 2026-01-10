<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        $activeTab = $request->get('tab', 'all'); // Default to 'all'

        // Always exclude students (they are managed separately in Students section)
        $query->whereDoesntHave('roles', function($q) {
            $q->where('name', 'student');
        });

        // Filter by tab (role-based filtering)
        if ($activeTab === 'teachers') {
            $query->role('teacher');
        } elseif ($activeTab === 'admins') {
            $query->role('admin');
        }
        // 'all' tab shows all users except students

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('mobile', 'like', '%' . $request->search . '%');
            });
        }

        // Additional filter by role (for backward compatibility) - exclude student role
        if ($request->has('role') && $request->role && $activeTab === 'all' && $request->role !== 'student') {
            $query->role($request->role);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $users = $query->with('roles')->latest()->paginate(15);
        // Exclude student role from available roles
        $roles = Role::where('name', '!=', 'student')->get();

        // Get statistics for each tab (excluding students)
        $stats = [
            'all' => User::whereDoesntHave('roles', function($q) {
                $q->where('name', 'student');
            })->count(),
            'teachers' => User::role('teacher')->count(),
            'admins' => User::role('admin')->count(),
            'active_teachers' => User::role('teacher')->where('status', 'active')->count(),
            'active_admins' => User::role('admin')->where('status', 'active')->count(),
        ];

        // Add pagination with tab parameter
        $users->appends($request->except('page'));

        return view('admin.users.index', compact('users', 'roles', 'activeTab', 'stats'));
    }

    public function create(Request $request)
    {
        // Exclude student role - students are managed separately
        $roles = Role::where('name', '!=', 'student')->get();
        $activeTab = $request->get('tab', 'all');
        return view('admin.users.create', compact('roles', 'activeTab'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'nullable|string|unique:users,mobile',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,inactive,suspended',
            'tab' => 'nullable|string|in:all,students,teachers,admins',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => trim($request->first_name . ' ' . $request->last_name), // Generate name for backwards compatibility
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'status' => $request->status,
            'email_verified_at' => now(),
        ]);

        $user->assignRole($request->role);

        // Redirect to the appropriate tab based on role (students are managed separately)
        $tab = $request->role === 'teacher' ? 'teachers' : ($request->role === 'admin' ? 'admins' : 'all');

        return redirect()->route('admin.users.index', ['tab' => $tab])
            ->with('success', 'User created successfully.');
    }

    public function show(Request $request, User $user)
    {
        // Prevent viewing students through this interface
        if ($user->hasRole('student')) {
            abort(404, 'Students are managed in the dedicated Students section.');
        }

        $user->load(['roles', 'enrollments.book', 'payments', 'deviceBindings']);
        $activeTab = $request->get('tab', 'all');
        return view('admin.users.show', compact('user', 'activeTab'));
    }

    public function edit(Request $request, User $user)
    {
        // Prevent editing students through this interface
        if ($user->hasRole('student')) {
            abort(404, 'Students are managed in the dedicated Students section.');
        }

        $user->load('roles');
        // Exclude student role - students are managed separately
        $roles = Role::where('name', '!=', 'student')->get();
        $activeTab = $request->get('tab', 'all');
        return view('admin.users.edit', compact('user', 'roles', 'activeTab'));
    }

    public function update(Request $request, User $user)
    {

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|unique:users,mobile,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name|not_in:student',
            'status' => 'required|in:active,inactive,suspended',
            'tab' => 'nullable|string|in:all,teachers,admins',
        ]);

        // Prevent changing to student role or editing students
        if ($user->hasRole('student') || $request->role === 'student') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Students are managed in the dedicated Students section.');
        }

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => trim($request->first_name . ' ' . $request->last_name), // Generate name for backwards compatibility
            'email' => $request->email,
            'mobile' => $request->mobile,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Prevent changing to student role or editing students
        if ($user->hasRole('student') || $request->role === 'student') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Students are managed in the dedicated Students section.');
        }

        // Update role (already validated that it's not student)
        $user->syncRoles([$request->role]);

        // Redirect to the appropriate tab based on role (students are managed separately)
        $tab = $request->role === 'teacher' ? 'teachers' : ($request->role === 'admin' ? 'admins' : 'all');

        return redirect()->route('admin.users.index', ['tab' => $tab])
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting students through this interface
        if ($user->hasRole('student')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Students are managed in the dedicated Students section.');
        }

        // Prevent deleting admin users
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            $activeTab = request()->get('tab', 'all');
            return redirect()->route('admin.users.index', ['tab' => $activeTab])
                ->with('error', 'Cannot delete the last admin user.');
        }

        // Determine tab before deletion
        $activeTab = request()->get('tab', 'all');
        if (!$activeTab || $activeTab === 'all') {
            // Determine tab from user role (no students tab)
            if ($user->hasRole('teacher')) {
                $activeTab = 'teachers';
            } elseif ($user->hasRole('admin')) {
                $activeTab = 'admins';
            }
        }

        $user->delete();

        return redirect()->route('admin.users.index', ['tab' => $activeTab])
            ->with('success', 'User deleted successfully.');
    }
}
