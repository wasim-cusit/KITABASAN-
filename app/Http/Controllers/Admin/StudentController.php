<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('student');

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

        // Filter by enrollment status
        if ($request->has('enrollment_status') && $request->enrollment_status) {
            if ($request->enrollment_status === 'enrolled') {
                $query->whereHas('enrollments');
            } elseif ($request->enrollment_status === 'not_enrolled') {
                $query->whereDoesntHave('enrollments');
            }
        }

        $students = $query->withCount(['enrollments', 'payments', 'lessonProgress'])
            ->with('enrollments.book')
            ->latest()
            ->paginate(15);

        // Statistics
        $stats = [
            'total' => User::role('student')->count(),
            'active' => User::role('student')->where('status', 'active')->count(),
            'inactive' => User::role('student')->where('status', 'inactive')->count(),
            'suspended' => User::role('student')->where('status', 'suspended')->count(),
            'enrolled' => User::role('student')->whereHas('enrollments')->count(),
            'not_enrolled' => User::role('student')->whereDoesntHave('enrollments')->count(),
        ];

        return view('admin.students.index', compact('students', 'stats'));
    }

    public function create()
    {
        return view('admin.students.create');
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

        $student = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => trim($request->first_name . ' ' . $request->last_name), // Generate name for backwards compatibility
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'status' => $request->status,
            'email_verified_at' => now(),
        ]);

        $student->assignRole('student');

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully.');
    }

    public function show($student)
    {
        $student = User::role('student')->findOrFail($student);

        $student->load([
            'roles',
            'enrollments.book',
            'enrollments' => function($query) {
                $query->latest()->limit(10);
            },
            'payments.book' => function($query) {
                $query->latest()->limit(10);
            },
            'deviceBindings',
            'lessonProgress.lesson',
        ]);

        // Statistics for this student
        $studentStats = [
            'total_enrollments' => $student->enrollments()->count(),
            'completed_courses' => $student->enrollments()->where('status', 'completed')->count(),
            'total_payments' => $student->payments()->sum('amount'),
            'total_lessons_completed' => $student->lessonProgress()->where('is_completed', true)->count(),
            'devices_count' => $student->deviceBindings()->count(),
        ];

        return view('admin.students.show', compact('student', 'studentStats'));
    }

    public function edit($student)
    {
        $student = User::role('student')->findOrFail($student);
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, $student)
    {
        $student = User::role('student')->findOrFail($student);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'mobile' => 'nullable|string|unique:users,mobile,' . $student->id,
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:active,inactive,suspended',
        ]);

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

        $student->update($data);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy($student)
    {
        $student = User::role('student')->findOrFail($student);
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }
}
