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
    public function store(Request $request, $bookId, $chapterId)
    {
        $book = Book::findOrFail($bookId);
        $chapter = Chapter::findOrFail($chapterId);

        // Check if teacher owns this course
        if ($book->teacher_id !== Auth::id() || $chapter->book_id !== $bookId) {
            abort(403, 'Unauthorized');
        }

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
            'is_free' => $request->has('is_free') ? true : false,
            'video_host' => $request->video_host,
            'video_id' => $request->video_id,
            'status' => $request->status ?? 'draft',
        ]);

        return redirect()->back()->with('success', 'Lesson created successfully.');
    }

    public function update(Request $request, $bookId, $chapterId, $lessonId)
    {
        $book = Book::findOrFail($bookId);
        $chapter = Chapter::findOrFail($chapterId);
        $lesson = Lesson::findOrFail($lessonId);

        // Check if teacher owns this course
        if ($book->teacher_id !== Auth::id() || $chapter->book_id !== $bookId || $lesson->chapter_id !== $chapterId) {
            abort(403, 'Unauthorized');
        }

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
            'is_free' => $request->has('is_free') ? true : false,
            'video_host' => $request->video_host,
            'video_id' => $request->video_id,
            'status' => $request->status ?? $lesson->status,
        ]);

        return redirect()->back()->with('success', 'Lesson updated successfully.');
    }

    public function destroy($bookId, $chapterId, $lessonId)
    {
        $book = Book::findOrFail($bookId);
        $chapter = Chapter::findOrFail($chapterId);
        $lesson = Lesson::findOrFail($lessonId);

        // Check if teacher owns this course
        if ($book->teacher_id !== Auth::id() || $chapter->book_id !== $bookId || $lesson->chapter_id !== $chapterId) {
            abort(403, 'Unauthorized');
        }

        $lesson->delete();

        return redirect()->back()->with('success', 'Lesson deleted successfully.');
    }
}
