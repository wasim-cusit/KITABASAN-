<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Subject;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Book::where('teacher_id', Auth::id())
            ->with(['subject.grade'])
            ->latest()
            ->paginate(10);

        return view('teacher.courses.index', compact('courses'));
    }

    public function create()
    {
        $grades = Grade::with('subjects')->get();
        return view('teacher.courses.create', compact('grades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'duration_months' => 'nullable|integer|min:1',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'subject_id' => $request->subject_id,
            'teacher_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price ?? 0,
            'is_free' => $request->has('is_free') ? true : false,
            'duration_months' => $request->duration_months ?? 12,
            'status' => 'draft',
        ];

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('courses', 'public');
        }

        $course = Book::create($data);

        return redirect()->route('teacher.courses.show', $course->id)
            ->with('success', 'Course created successfully. Now add chapters and lessons.');
    }

    public function show($id)
    {
        $course = Book::with(['chapters.lessons.topics', 'subject.grade'])
            ->where('teacher_id', Auth::id())
            ->findOrFail($id);

        return view('teacher.courses.show', compact('course'));
    }

    public function edit($id)
    {
        $course = Book::where('teacher_id', Auth::id())->findOrFail($id);
        $grades = Grade::with('subjects')->get();

        return view('teacher.courses.edit', compact('course', 'grades'));
    }

    public function update(Request $request, $id)
    {
        $course = Book::where('teacher_id', Auth::id())->findOrFail($id);

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'duration_months' => 'nullable|integer|min:1',
            'cover_image' => 'nullable|image|max:2048',
            'status' => 'nullable|in:draft,published',
        ]);

        $data = [
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price ?? 0,
            'is_free' => $request->has('is_free') ? true : false,
            'duration_months' => $request->duration_months ?? $course->duration_months,
        ];

        if ($request->has('status')) {
            $data['status'] = $request->status;
        }

        if ($request->hasFile('cover_image')) {
            if ($course->cover_image) {
                Storage::disk('public')->delete($course->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('courses', 'public');
        }

        $course->update($data);

        return redirect()->route('teacher.courses.show', $course->id)
            ->with('success', 'Course updated successfully.');
    }

    public function destroy($id)
    {
        $course = Book::where('teacher_id', Auth::id())->findOrFail($id);

        if ($course->cover_image) {
            Storage::disk('public')->delete($course->cover_image);
        }

        $course->delete();

        return redirect()->route('teacher.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
