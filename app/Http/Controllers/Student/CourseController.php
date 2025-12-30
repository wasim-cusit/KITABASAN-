<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Book::where('status', 'published')
            ->with(['subject.grade', 'teacher'])
            ->latest()
            ->paginate(12);

        return view('student.courses.index', compact('courses'));
    }

    public function show($id)
    {
        $course = Book::with(['chapters.lessons.topics', 'subject.grade', 'teacher'])
            ->findOrFail($id);

        if ($course->status !== 'published') {
            abort(404);
        }

        $user = Auth::user();
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('book_id', $id)
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();

        // Count free and paid content
        $freeChapters = $course->chapters->where('is_free', true)->count();
        $paidChapters = $course->chapters->where('is_free', false)->count();
        $totalLessons = $course->chapters->sum(fn($ch) => $ch->lessons->count());
        $freeLessons = $course->chapters->sum(fn($ch) => $ch->lessons->where('is_free', true)->count());

        return view('student.courses.show', compact('course', 'enrollment', 'freeChapters', 'paidChapters', 'totalLessons', 'freeLessons'));
    }

    public function enroll($id)
    {
        $course = Book::findOrFail($id);
        $user = Auth::user();

        if ($course->is_free) {
            // Free enrollment
            $enrollment = CourseEnrollment::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'book_id' => $course->id,
                ],
                [
                    'status' => 'active',
                    'enrolled_at' => now(),
                    'expires_at' => now()->addMonths($course->duration_months),
                ]
            );

            return redirect()->route('student.learning.index', $course->id)
                ->with('success', 'Successfully enrolled in the course!');
        } else {
            // Paid course - redirect to payment
            return redirect()->route('student.payments.store', ['course_id' => $course->id]);
        }
    }
}
