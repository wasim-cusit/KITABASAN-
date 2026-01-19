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
        // Get courses where teacher is main teacher OR co-teacher
        $courses = Book::where(function($query) {
                $query->where('teacher_id', Auth::id())
                      ->orWhereHas('teachers', function($q) {
                          $q->where('users.id', Auth::id());
                      });
            })
            ->with(['subject.grade', 'teacher'])
            ->latest()
            ->paginate(10);

        return view('teacher.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('teacher.courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'grade_name' => 'required|string|max:255',
            'subject_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:200',
            'what_you_will_learn' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'access_duration_months' => 'nullable|integer|min:1',
            'thumbnail' => 'nullable|image|max:10240', // 10MB limit
            'cover_image' => 'nullable|image|max:10240', // 10MB limit
            'language' => 'nullable|in:en,ur,ar,other',
            'difficulty_level' => 'nullable|in:beginner,intermediate,advanced,all',
        ]);

        // Try to find or create Grade and Subject by name
        $grade = \App\Models\Grade::firstOrCreate(
            ['name' => $request->grade_name],
            ['slug' => \Illuminate\Support\Str::slug($request->grade_name), 'is_active' => true]
        );

        $subject = null;
        if ($grade) {
            $subject = \App\Models\Subject::firstOrCreate(
                [
                    'name' => $request->subject_name,
                    'grade_id' => $grade->id
                ],
                [
                    'slug' => \Illuminate\Support\Str::slug($request->subject_name),
                    'is_active' => true
                ]
            );
        }

        // Generate slug
        $slug = \Illuminate\Support\Str::slug($request->title);
        $slugCount = Book::where('slug', $slug)->count();
        if ($slugCount > 0) {
            $slug = $slug . '-' . ($slugCount + 1);
        }

        // Process learning objectives from what_you_will_learn field
        $learning_objectives = null;
        if ($request->has('what_you_will_learn') && !empty($request->what_you_will_learn)) {
            // Convert text to array (split by newlines or bullets)
            $objectives = preg_split('/\n|•|\*|-/', $request->what_you_will_learn);
            $objectives = array_map('trim', $objectives);
            $objectives = array_filter($objectives); // Remove empty items
            if (!empty($objectives)) {
                $learning_objectives = array_values($objectives);
            }
        }

        $data = [
            'subject_id' => $subject ? $subject->id : null,
            'grade_name' => $request->grade_name,
            'subject_name' => $request->subject_name,
            'teacher_id' => Auth::id(), // Teacher is the creator
            'title' => $request->title,
            'slug' => $slug,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'what_you_will_learn' => $request->what_you_will_learn,
            'learning_objectives' => $learning_objectives,
            'language' => $request->language ?? 'en',
            'difficulty_level' => $request->difficulty_level ?? 'all',
            'price' => $request->has('is_free') && $request->is_free ? 0 : ($request->price ?? 0),
            'is_free' => $request->has('is_free') ? true : false,
            'access_duration_months' => $request->access_duration_months ?? 12,
            'status' => 'draft', // Teacher courses always start as draft
        ];

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('courses/covers', 'public');
        }

        $course = Book::create($data);

        return redirect()->route('teacher.courses.show', $course->id)
            ->with('success', 'Course created successfully. Now add chapters and lessons.');
    }

    public function show($id)
    {
        $course = Book::with(['chapters.lessons.topics', 'subject.grade', 'teacher'])
            ->where(function($query) {
                $query->where('teacher_id', Auth::id())
                      ->orWhereHas('teachers', function($q) {
                          $q->where('users.id', Auth::id());
                      });
            })
            ->findOrFail($id);

        // Check if teacher has access to this course
        if (!$course->hasTeacher(Auth::id())) {
            abort(403, 'You do not have permission to view this course.');
        }

        return view('teacher.courses.show', compact('course'));
    }

    public function edit($id)
    {
        $course = Book::findOrFail($id);

        // Check if teacher has access (only main teacher can edit)
        if ($course->teacher_id != Auth::id()) {
            abort(403, 'Only the main teacher can edit this course.');
        }

        return view('teacher.courses.edit', compact('course'));
    }

    public function update(Request $request, $id)
    {
        $course = Book::findOrFail($id);

        // Check if teacher has access (only main teacher can edit)
        if ($course->teacher_id != Auth::id()) {
            abort(403, 'Only the main teacher can edit this course.');
        }

        $request->validate([
            'grade_name' => 'required|string|max:255',
            'subject_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:200',
            'what_you_will_learn' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'access_duration_months' => 'nullable|integer|min:1',
            'thumbnail' => 'nullable|image|max:10240', // 10MB limit
            'cover_image' => 'nullable|image|max:10240', // 10MB limit
            'status' => 'nullable|in:draft,pending,published',
        ]);

        // Try to find or create Grade and Subject by name
        $grade = \App\Models\Grade::firstOrCreate(
            ['name' => $request->grade_name],
            ['slug' => \Illuminate\Support\Str::slug($request->grade_name), 'is_active' => true]
        );

        $subject = null;
        if ($grade) {
            $subject = \App\Models\Subject::firstOrCreate(
                [
                    'name' => $request->subject_name,
                    'grade_id' => $grade->id
                ],
                [
                    'slug' => \Illuminate\Support\Str::slug($request->subject_name),
                    'is_active' => true
                ]
            );
        }

        // Process learning objectives from what_you_will_learn field
        $learning_objectives = null;
        if ($request->has('what_you_will_learn') && !empty($request->what_you_will_learn)) {
            // Convert text to array (split by newlines or bullets)
            $objectives = preg_split('/\n|•|\*|-/', $request->what_you_will_learn);
            $objectives = array_map('trim', $objectives);
            $objectives = array_filter($objectives); // Remove empty items
            if (!empty($objectives)) {
                $learning_objectives = array_values($objectives);
            }
        }

        $data = [
            'subject_id' => $subject ? $subject->id : $course->subject_id,
            'grade_name' => $request->grade_name,
            'subject_name' => $request->subject_name,
            'title' => $request->title,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'what_you_will_learn' => $request->what_you_will_learn,
            'learning_objectives' => $learning_objectives,
            'price' => $request->has('is_free') && $request->is_free ? 0 : ($request->price ?? $course->price),
            'is_free' => $request->has('is_free') ? true : false,
            'access_duration_months' => $request->access_duration_months ?? $course->access_duration_months,
        ];

        // Allow teachers to set status to draft, pending, or published
        if ($request->has('status') && in_array($request->status, ['draft', 'pending', 'published'])) {
            $data['status'] = $request->status;
        }

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        if ($request->hasFile('cover_image')) {
            if ($course->cover_image) {
                Storage::disk('public')->delete($course->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('courses/covers', 'public');
        }

        $course->update($data);

        return redirect()->route('teacher.courses.show', $course->id)
            ->with('success', 'Course updated successfully.');
    }

    public function destroy($id)
    {
        $course = Book::findOrFail($id);

        // Only main teacher can delete the course
        if ($course->teacher_id != Auth::id()) {
            abort(403, 'Only the main teacher can delete this course.');
        }

        // Delete associated files
        if ($course->cover_image) {
            Storage::disk('public')->delete($course->cover_image);
        }
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return redirect()->route('teacher.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
