<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with(['teacher', 'subject.grade']);

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by subject
        if ($request->has('subject_id') && $request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }

        $courses = $query->latest()->paginate(15);
        $subjects = Subject::with('grade')->get();

        return view('admin.courses.index', compact('courses', 'subjects'));
    }

    public function create()
    {
        $grades = Grade::with('subjects')->get();
        return view('admin.courses.create', compact('grades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'duration_months' => 'nullable|integer|min:1',
            'cover_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $data = [
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price ?? 0,
            'is_free' => $request->has('is_free') ? true : false,
            'duration_months' => $request->duration_months ?? 12,
            'status' => $request->status,
        ];

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('courses', 'public');
        }

        Book::create($data);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function show($id)
    {
        $course = Book::with(['teacher', 'subject.grade', 'chapters.lessons.topics'])
            ->findOrFail($id);

        return view('admin.courses.show', compact('course'));
    }

    public function edit($id)
    {
        $course = Book::findOrFail($id);
        $grades = Grade::with('subjects')->get();
        $teachers = \App\Models\User::role('teacher')->get();

        return view('admin.courses.edit', compact('course', 'grades', 'teachers'));
    }

    public function update(Request $request, $id)
    {
        $course = Book::findOrFail($id);

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'duration_months' => 'nullable|integer|min:1',
            'cover_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $data = [
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price ?? 0,
            'is_free' => $request->has('is_free') ? true : false,
            'duration_months' => $request->duration_months ?? $course->duration_months,
            'status' => $request->status,
        ];

        if ($request->hasFile('cover_image')) {
            if ($course->cover_image) {
                Storage::disk('public')->delete($course->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('courses', 'public');
        }

        $course->update($data);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy($id)
    {
        $course = Book::findOrFail($id);

        if ($course->cover_image) {
            Storage::disk('public')->delete($course->cover_image);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    public function approve($id)
    {
        $course = Book::findOrFail($id);
        $course->update(['status' => 'published']);

        return redirect()->back()
            ->with('success', 'Course approved and published.');
    }

    public function reject($id)
    {
        $course = Book::findOrFail($id);
        $course->update(['status' => 'draft']);

        return redirect()->back()
            ->with('success', 'Course rejected and set to draft.');
    }
}
