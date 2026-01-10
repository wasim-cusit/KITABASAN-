<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get all students enrolled in teacher's courses
        $query = User::whereHas('enrollments.book', function($query) use ($user) {
            $query->where('teacher_id', $user->id);
        })->with(['enrollments' => function($query) use ($user) {
            $query->whereHas('book', function($q) use ($user) {
                $q->where('teacher_id', $user->id);
            })->with('book');
        }]);

        // Search filter
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('mobile', 'like', '%' . $request->search . '%');
            });
        }

        $students = $query->withCount(['enrollments' => function($query) use ($user) {
            $query->whereHas('book', function($q) use ($user) {
                $q->where('teacher_id', $user->id);
            });
        }])->paginate(15);

        return view('teacher.students.index', compact('students'));
    }

    public function show($id)
    {
        $teacher = Auth::user();

        // Get student
        $student = User::findOrFail($id);

        // Get all enrollments in teacher's courses
        $enrollments = CourseEnrollment::where('user_id', $id)
            ->whereHas('book', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->with(['book.subject', 'payment'])
            ->latest()
            ->get();

        // Get statistics
        $stats = [
            'total_courses' => $enrollments->count(),
            'active_enrollments' => $enrollments->where('status', 'active')->count(),
            'average_progress' => $enrollments->avg('progress_percentage') ?? 0,
        ];

        return view('teacher.students.show', compact('student', 'enrollments', 'stats'));
    }
}
