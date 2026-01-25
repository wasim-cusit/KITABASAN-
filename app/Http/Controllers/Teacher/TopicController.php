<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TopicController extends Controller
{
    public function index($bookId, $chapterId, $lessonId)
    {
        $book = Book::findOrFail($bookId);
        $chapter = Chapter::findOrFail($chapterId);
        $lesson = Lesson::findOrFail($lessonId);

        // Check if teacher owns this course or is a co-teacher
        if (!$book->hasTeacher(Auth::id()) || $chapter->book_id != $bookId || $lesson->chapter_id != $chapterId) {
            abort(403, 'Unauthorized');
        }

        // Redirect to course show page (topics are managed on the course page)
        return redirect()->route('teacher.courses.show', $bookId)
            ->with('info', 'Lesson: ' . $lesson->title);
    }

    public function store(Request $request, $bookId, $chapterId, $lessonId)
    {
        $book = Book::findOrFail($bookId);
        $chapter = Chapter::findOrFail($chapterId);
        $lesson = Lesson::findOrFail($lessonId);

        // Check if teacher owns this course or is a co-teacher
        if (!$book->hasTeacher(Auth::id()) || $chapter->book_id != $bookId || $lesson->chapter_id != $chapterId) {
            abort(403, 'Unauthorized');
        }

        // Normalize is_free value before validation
        $request->merge([
            'is_free' => $request->has('is_free') && $request->input('is_free') !== '0' && $request->input('is_free') !== 'off' && $request->input('is_free') !== false,
        ]);

        $validationRules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_free' => 'boolean',
            'type' => 'nullable|in:lecture,quiz,mcq',
            'video_host' => 'nullable|in:youtube,bunny,upload',
            'video_id' => 'nullable|string',
        ];

        // Only validate video_file if video_host is 'upload'
        if ($request->video_host === 'upload') {
            $validationRules['video_file'] = 'required|file|mimes:mp4,webm,mov,avi,flv,wmv,mkv|max:5242880'; // 5GB max
        } else {
            $validationRules['video_file'] = 'nullable|file|mimes:mp4,webm,mov,avi,flv,wmv,mkv|max:5242880';
        }

        $request->validate($validationRules);

        $data = [
            'lesson_id' => $lessonId,
            'title' => $request->title,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'is_free' => $request->boolean('is_free'),
            'type' => $request->type ?? 'lecture',
            'video_host' => $request->video_host,
        ];

        // Handle video based on source
        if ($request->video_host === 'youtube' && $request->video_id) {
            // Extract YouTube video ID from URL if full URL provided
            $videoId = $this->extractYouTubeId($request->video_id);
            $data['video_id'] = $videoId;
        } elseif ($request->video_host === 'bunny' && $request->video_id) {
            $data['video_id'] = $request->video_id;
        } elseif ($request->video_host === 'upload' && $request->hasFile('video_file')) {
            $file = $request->file('video_file');

            // Validate file extension as fallback
            $allowedExtensions = ['mp4', 'webm', 'mov', 'avi', 'flv', 'wmv', 'mkv'];
            $extension = strtolower($file->getClientOriginalExtension());

            if (!in_array($extension, $allowedExtensions)) {
                return redirect()->back()->withErrors([
                    'video_file' => 'Invalid video file format. Allowed formats: ' . implode(', ', $allowedExtensions)
                ])->withInput();
            }

            $path = $file->store('videos/topics', 'public');
            $data['video_file'] = $path;
            $data['video_size'] = $file->getSize();
            $data['video_mime_type'] = $file->getMimeType();
        }

        $topic = Topic::create($data);

        return redirect()->route('teacher.courses.show', $bookId)->with('success', 'Topic created successfully.');
    }

    public function update(Request $request, $bookId, $chapterId, $lessonId, $topicId)
    {
        $book = Book::findOrFail($bookId);
        $chapter = Chapter::findOrFail($chapterId);
        $lesson = Lesson::findOrFail($lessonId);
        $topic = Topic::findOrFail($topicId);

        // Check if teacher owns this course or is a co-teacher
        if (!$book->hasTeacher(Auth::id()) || $chapter->book_id != $bookId || $lesson->chapter_id != $chapterId || $topic->lesson_id != $lessonId) {
            abort(403, 'Unauthorized');
        }

        // Normalize is_free value before validation
        $request->merge([
            'is_free' => $request->has('is_free') && $request->input('is_free') !== '0' && $request->input('is_free') !== 'off' && $request->input('is_free') !== false,
        ]);

        $validationRules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_free' => 'boolean',
            'type' => 'nullable|in:lecture,quiz,mcq',
            'video_host' => 'nullable|in:youtube,bunny,upload',
            'video_id' => 'nullable|string',
        ];

        // Only validate video_file if video_host is 'upload' and a new file is being uploaded
        if ($request->video_host === 'upload') {
            if ($request->hasFile('video_file')) {
                $validationRules['video_file'] = 'required|file|mimes:mp4,webm,mov,avi,flv,wmv,mkv|max:5242880'; // 5GB max
            } else {
                // If switching to upload but no file provided, and topic already has a video, allow it
                $validationRules['video_file'] = 'nullable|file|mimes:mp4,webm,mov,avi,flv,wmv,mkv|max:5242880';
            }
        } else {
            $validationRules['video_file'] = 'nullable|file|mimes:mp4,webm,mov,avi,flv,wmv,mkv|max:5242880';
        }

        $request->validate($validationRules);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'order' => $request->order ?? $topic->order,
            'is_free' => $request->boolean('is_free'),
            'type' => $request->type ?? $topic->type,
            'video_host' => $request->video_host,
        ];

        // Handle video based on source
        if ($request->video_host === 'youtube') {
            if ($request->filled('video_id')) {
                // Extract YouTube video ID from URL if full URL provided
                $videoId = $this->extractYouTubeId($request->video_id);
                $data['video_id'] = $videoId;
            } elseif ($request->video_host === $topic->video_host && $topic->video_id) {
                // If video_host hasn't changed and no new video_id provided, keep existing video_id
                $data['video_id'] = $topic->video_id;
            } else {
                // If switching to YouTube but no video_id provided, clear it
                $data['video_id'] = null;
            }
            // Clear upload fields if switching to YouTube
            $data['video_file'] = null;
            $data['video_size'] = null;
            $data['video_mime_type'] = null;
        } elseif ($request->video_host === 'bunny') {
            if ($request->filled('video_id')) {
                $data['video_id'] = $request->video_id;
            } elseif ($request->video_host === $topic->video_host && $topic->video_id) {
                // If video_host hasn't changed and no new video_id provided, keep existing video_id
                $data['video_id'] = $topic->video_id;
            } else {
                // If switching to Bunny but no video_id provided, clear it
                $data['video_id'] = null;
            }
            // Clear upload fields if switching to Bunny
            $data['video_file'] = null;
            $data['video_size'] = null;
            $data['video_mime_type'] = null;
        } elseif ($request->video_host === 'upload' && $request->hasFile('video_file')) {
            $file = $request->file('video_file');

            // Validate file extension as fallback
            $allowedExtensions = ['mp4', 'webm', 'mov', 'avi', 'flv', 'wmv', 'mkv'];
            $extension = strtolower($file->getClientOriginalExtension());

            if (!in_array($extension, $allowedExtensions)) {
                return redirect()->back()->withErrors([
                    'video_file' => 'Invalid video file format. Allowed formats: ' . implode(', ', $allowedExtensions)
                ])->withInput();
            }

            // Delete old video file if exists
            if ($topic->video_file) {
                Storage::disk('public')->delete($topic->video_file);
            }
            $path = $file->store('videos/topics', 'public');
            $data['video_file'] = $path;
            $data['video_size'] = $file->getSize();
            $data['video_mime_type'] = $file->getMimeType();
            $data['video_id'] = null;
        } elseif (!$request->video_host) {
            // Clear all video fields if no video source selected
            $data['video_id'] = null;
            $data['video_file'] = null;
            $data['video_size'] = null;
            $data['video_mime_type'] = null;
        }

        $topic->update($data);

        return redirect()->route('teacher.courses.show', $bookId)->with('success', 'Topic updated successfully.');
    }

    public function destroy($bookId, $chapterId, $lessonId, $topicId)
    {
        $book = Book::findOrFail($bookId);
        $chapter = Chapter::findOrFail($chapterId);
        $lesson = Lesson::findOrFail($lessonId);
        $topic = Topic::findOrFail($topicId);

        // Check if teacher owns this course or is a co-teacher
        if (!$book->hasTeacher(Auth::id()) || $chapter->book_id != $bookId || $lesson->chapter_id != $chapterId || $topic->lesson_id != $lessonId) {
            abort(403, 'Unauthorized');
        }

        // Delete video file if exists
        if ($topic->video_file) {
            Storage::disk('public')->delete($topic->video_file);
        }

        $topic->delete();

        return redirect()->route('teacher.courses.show', $bookId)->with('success', 'Topic deleted successfully.');
    }

    /**
     * Extract YouTube video ID from URL or return as-is if already an ID
     */
    private function extractYouTubeId($input): string
    {
        // If it's already just an ID (no special characters except dashes and underscores)
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) {
            return $input;
        }

        // Try to extract from various YouTube URL formats
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/',
            '/youtu\.be\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/v\/([a-zA-Z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input, $matches)) {
                return $matches[1];
            }
        }

        // If no pattern matches, return original (might be invalid, but let validation handle it)
        return $input;
    }
}

