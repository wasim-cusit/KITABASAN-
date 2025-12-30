<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::where('status', 'published')
            ->with(['subject.grade', 'teacher']);

        // Filter by grade
        if ($request->has('grade')) {
            $query->whereHas('subject', function($q) use ($request) {
                $q->where('grade_id', $request->grade);
            });
        }

        // Filter by subject
        if ($request->has('subject')) {
            $query->where('subject_id', $request->subject);
        }

        // Filter by free/paid
        if ($request->has('type')) {
            if ($request->type === 'free') {
                $query->where('is_free', true);
            } elseif ($request->type === 'paid') {
                $query->where('is_free', false);
            }
        }

        // Search
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $courses = $query->latest()->paginate(12);
        $grades = Grade::with('subjects')->get();
        $subjects = Subject::with('grade')->get();

        return view('public.courses.index', compact('courses', 'grades', 'subjects'));
    }

    public function show($id)
    {
        $course = Book::with(['subject.grade', 'chapters.lessons', 'teacher'])
            ->findOrFail($id);

        if ($course->status !== 'published') {
            abort(404);
        }

        $relatedCourses = Book::where('status', 'published')
            ->where('subject_id', $course->subject_id)
            ->where('id', '!=', $course->id)
            ->limit(4)
            ->get();

        return view('public.courses.show', compact('course', 'relatedCourses'));
    }
}
