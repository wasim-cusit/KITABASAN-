<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('teacher');

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

        $teachers = $query->with('roles')->latest()->paginate(15);

        // Get statistics
        $stats = [
            'total' => User::role('teacher')->count(),
            'active' => User::role('teacher')->where('status', 'active')->count(),
            'inactive' => User::role('teacher')->where('status', 'inactive')->count(),
            'suspended' => User::role('teacher')->where('status', 'suspended')->count(),
        ];

        // Add pagination
        $teachers->appends($request->except('page'));

        return view('admin.teachers.index', compact('teachers', 'stats'));
    }

    public function create()
    {
        return view('admin.teachers.create');
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

        $user->assignRole('teacher');

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher created successfully.');
    }

    public function show(User $user)
    {
        // Ensure this is a teacher
        if (!$user->hasRole('teacher')) {
            abort(404, 'User is not a teacher.');
        }

        $user->load(['roles', 'enrollments.book', 'payments', 'deviceBindings']);
        return view('admin.teachers.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Ensure this is a teacher
        if (!$user->hasRole('teacher')) {
            abort(404, 'User is not a teacher.');
        }

        $user->load('roles');
        return view('admin.teachers.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Ensure this is a teacher
        if (!$user->hasRole('teacher')) {
            abort(404, 'User is not a teacher.');
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

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher updated successfully.');
    }

    public function destroy(User $user)
    {
        // Ensure this is a teacher
        if (!$user->hasRole('teacher')) {
            abort(404, 'User is not a teacher.');
        }

        $user->delete();

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher deleted successfully.');
    }
}
