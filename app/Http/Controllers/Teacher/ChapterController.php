<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChapterController extends Controller
{
    public function store(Request $request, $bookId)
    {
        $book = Book::findOrFail($bookId);

        // Check if teacher owns this course or is a co-teacher
        if (!$book->hasTeacher(Auth::id())) {
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
        ]);

        $chapter = Chapter::create([
            'book_id' => $bookId,
            'title' => $request->title,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'is_free' => $request->boolean('is_free'),
        ]);

        return redirect()->back()->with('success', 'Chapter created successfully.');
    }

    public function show($bookId, $chapterId)
    {
        $book = Book::findOrFail($bookId);
        $chapter = Chapter::findOrFail($chapterId);

        // Check if teacher owns this course or is a co-teacher
        if (!$book->hasTeacher(Auth::id()) || $chapter->book_id != $bookId) {
            abort(403, 'Unauthorized');
        }

        // Redirect to course show page (chapters are managed on the course page)
        return redirect()->route('teacher.courses.show', $bookId)
            ->with('info', 'Chapter: ' . $chapter->title);
    }

    public function update(Request $request, $bookId, $chapterId)
    {
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
        ]);

        $chapter->update([
            'title' => $request->title,
            'description' => $request->description,
            'order' => $request->order ?? $chapter->order,
            'is_free' => $request->boolean('is_free'),
        ]);

        return redirect()->back()->with('success', 'Chapter updated successfully.');
    }

    public function destroy($bookId, $chapterId)
    {
        $book = Book::findOrFail($bookId);
        $chapter = Chapter::findOrFail($chapterId);

        // Check if teacher owns this course or is a co-teacher
        if (!$book->hasTeacher(Auth::id()) || $chapter->book_id != $bookId) {
            abort(403, 'Unauthorized');
        }

        $chapter->delete();

        return redirect()->back()->with('success', 'Chapter deleted successfully.');
    }
}

