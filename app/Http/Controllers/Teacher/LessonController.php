<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function index()
    {
        // Redirect to courses index if accessed directly
        return redirect()->route('teacher.courses.index');
    }

    public function show($id)
    {
        $lesson = Lesson::with(['chapter.book'])->findOrFail($id);
        
        // Check if teacher owns this course
        if ($lesson->chapter->book->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return redirect()->route('teacher.courses.show', $lesson->chapter->book->id)
            ->with('info', 'Lesson: ' . $lesson->title);
    }

    public function edit($id)
    {
        $lesson = Lesson::with(['chapter.book'])->findOrFail($id);
        
        // Check if teacher owns this course
        if ($lesson->chapter->book->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Show edit modal or redirect to course page with lesson edit form
        return redirect()->route('teacher.courses.show', $lesson->chapter->book->id)
            ->with('edit_lesson_id', $lesson->id);
    }

    public function create()
    {
        // Redirect to courses index if accessed directly
        return redirect()->route('teacher.courses.index');
    }

    public function store(Request $request, $bookId = null, $chapterId = null)
    {
        // Handle both resource route and nested route
        if (!$bookId || !$chapterId) {
            // If called from resource route, get from request
            $bookId = $request->input('book_id');
            $chapterId = $request->input('chapter_id');
        }

        if (!$bookId || !$chapterId) {
            return redirect()->back()->with('error', 'Invalid course or chapter.');
        }

        $book = Book::findOrFail($bookId);
        $chapter = Chapter::findOrFail($chapterId);

        // Check if teacher owns this course or is a co-teacher
        if (!$book->hasTeacher(Auth::id()) || $chapter->book_id != $bookId) {
            abort(403, 'Unauthorized');
        }

        // Normalize is_free value before validation
        $request->merge([
            'is_free' => $request->has('is_free') && $request->input('is_free') !== '0' && $request->input('is_free') !== 'off' && $request->input('is_free') !== false,
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_free' => 'boolean',
            'video_host' => 'nullable|in:youtube,bunny,upload',
            'video_id' => 'nullable|string',
            'status' => 'nullable|in:draft,published',
        ]);

        $lesson = Lesson::create([
            'chapter_id' => $chapterId,
            'title' => $request->title,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'is_free' => $request->boolean('is_free'),
            'video_host' => $request->video_host,
            'video_id' => $request->video_id,
            'status' => $request->status ?? 'draft',
        ]);

        return redirect()->back()->with('success', 'Lesson created successfully.');
    }

    public function update(Request $request, $bookId, $chapterId, $lessonId)
    {
        // Nested route: update($request, $bookId, $chapterId, $lessonId)
        $book = Book::findOrFail($bookId);
        $chapter = Chapter::findOrFail($chapterId);
        $lesson = Lesson::findOrFail($lessonId);

        // Check if teacher owns this course
        if ($book->teacher_id !== Auth::id() || $chapter->book_id != $book->id || $lesson->chapter_id != $chapter->id) {
            abort(403, 'Unauthorized');
        }

        // Normalize is_free value before validation
        $request->merge([
            'is_free' => $request->has('is_free') && $request->input('is_free') !== '0' && $request->input('is_free') !== 'off' && $request->input('is_free') !== false,
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_free' => 'boolean',
            'video_host' => 'nullable|in:youtube,bunny,upload',
            'video_id' => 'nullable|string',
            'status' => 'nullable|in:draft,published',
        ]);

        $lesson->update([
            'title' => $request->title,
            'description' => $request->description,
            'order' => $request->order ?? $lesson->order,
            'is_free' => $request->boolean('is_free'),
            'video_host' => $request->video_host,
            'video_id' => $request->video_id,
            'status' => $request->status ?? $lesson->status,
        ]);

        return redirect()->back()->with('success', 'Lesson updated successfully.');
    }

    public function destroy($bookId, $chapterId, $lessonId)
    {
        // Nested route: destroy($bookId, $chapterId, $lessonId)
        $book = Book::findOrFail($bookId);
        $chapter = Chapter::findOrFail($chapterId);
        $lesson = Lesson::findOrFail($lessonId);

        // Check if teacher owns this course
        if ($book->teacher_id !== Auth::id() || $chapter->book_id != $book->id || $lesson->chapter_id != $chapter->id) {
            abort(403, 'Unauthorized');
        }

        $lesson->delete();

        return redirect()->back()->with('success', 'Lesson deleted successfully.');
    }
}
