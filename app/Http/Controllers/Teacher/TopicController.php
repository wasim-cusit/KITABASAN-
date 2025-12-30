<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicController extends Controller
{
    public function store(Request $request, $bookId, $chapterId, $lessonId)
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
            'type' => 'nullable|in:lecture,quiz,mcq',
            'video_host' => 'nullable|in:youtube,bunny,upload',
            'video_id' => 'nullable|string',
        ]);

        $topic = Topic::create([
            'lesson_id' => $lessonId,
            'title' => $request->title,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'is_free' => $request->has('is_free') ? true : false,
            'type' => $request->type ?? 'lecture',
            'video_host' => $request->video_host,
            'video_id' => $request->video_id,
        ]);

        return redirect()->back()->with('success', 'Topic created successfully.');
    }

    public function update(Request $request, $bookId, $chapterId, $lessonId, $topicId)
    {
        $book = Book::findOrFail($bookId);
        $chapter = Chapter::findOrFail($chapterId);
        $lesson = Lesson::findOrFail($lessonId);
        $topic = Topic::findOrFail($topicId);

        // Check if teacher owns this course
        if ($book->teacher_id !== Auth::id() || $chapter->book_id !== $bookId || $lesson->chapter_id !== $chapterId || $topic->lesson_id !== $lessonId) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_free' => 'boolean',
            'type' => 'nullable|in:lecture,quiz,mcq',
            'video_host' => 'nullable|in:youtube,bunny,upload',
            'video_id' => 'nullable|string',
        ]);

        $topic->update([
            'title' => $request->title,
            'description' => $request->description,
            'order' => $request->order ?? $topic->order,
            'is_free' => $request->has('is_free') ? true : false,
            'type' => $request->type ?? $topic->type,
            'video_host' => $request->video_host,
            'video_id' => $request->video_id,
        ]);

        return redirect()->back()->with('success', 'Topic updated successfully.');
    }

    public function destroy($bookId, $chapterId, $lessonId, $topicId)
    {
        $book = Book::findOrFail($bookId);
        $chapter = Chapter::findOrFail($chapterId);
        $lesson = Lesson::findOrFail($lessonId);
        $topic = Topic::findOrFail($topicId);

        // Check if teacher owns this course
        if ($book->teacher_id !== Auth::id() || $chapter->book_id !== $bookId || $lesson->chapter_id !== $chapterId || $topic->lesson_id !== $lessonId) {
            abort(403, 'Unauthorized');
        }

        $topic->delete();

        return redirect()->back()->with('success', 'Topic deleted successfully.');
    }
}

