<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Lesson;
use App\Models\ContentItem;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContentItemController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    public function store(Request $request, $bookId, $chapterId, $lessonId)
    {
        $book = Book::findOrFail($bookId);
        $lesson = Lesson::with('chapter')->findOrFail($lessonId);

        // Check if teacher owns this course
        if ($book->teacher_id !== Auth::id() || $lesson->chapter->book_id !== $bookId) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'content_type' => 'required|in:video,quiz,document,assignment',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_index' => 'nullable|integer',
            'is_preview' => 'boolean',
            
            // Video fields
            'video_provider' => 'required_if:content_type,video|in:youtube,vimeo,upload,bunny',
            'video_id' => 'required_if:video_provider,youtube,bunny|nullable|string',
            'youtube_privacy' => 'nullable|in:public,unlisted,private',
            'video_file' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv|max:10240',
            
            // Document fields
            'document_file' => 'nullable|file|mimes:pdf,doc,docx,txt|max:5120',
            'document_type' => 'nullable|string',
            
            // Quiz fields
            'quiz_id' => 'nullable|exists:quizzes,id',
        ]);

        $data = [
            'lesson_id' => $lessonId,
            'content_type' => $request->content_type,
            'title' => $request->title,
            'description' => $request->description,
            'order_index' => $request->order_index ?? 0,
            'is_preview' => $request->has('is_preview') ? true : false,
        ];

        // Handle video content
        if ($request->content_type === 'video') {
            $data['video_provider'] = $request->video_provider;
            $data['youtube_privacy'] = $request->youtube_privacy ?? 'public';

            if ($request->video_provider === 'youtube' && $request->video_id) {
                // Validate YouTube video
                $validation = $this->videoService->validateYouTubeVideo($request->video_id, $request->youtube_privacy);
                if (!$validation['valid']) {
                    return redirect()->back()->with('error', $validation['error'] ?? 'Invalid YouTube video');
                }
                $data['video_id'] = $request->video_id;
                $data['duration'] = $validation['duration'] ?? 0;
            } elseif ($request->video_provider === 'upload' && $request->hasFile('video_file')) {
                // Upload video to cloud storage
                $uploadResult = $this->videoService->uploadVideoToCloud($request->file('video_file'));
                if (!$uploadResult['success']) {
                    return redirect()->back()->with('error', 'Video upload failed: ' . ($uploadResult['error'] ?? 'Unknown error'));
                }
                $data['video_file'] = $uploadResult['path'];
                $data['video_cloud_url'] = $uploadResult['url'];
            } elseif ($request->video_provider === 'bunny' && $request->video_id) {
                $data['video_id'] = $request->video_id;
            }
        }

        // Handle document content
        if ($request->content_type === 'document' && $request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $path = $file->store('documents', 'public');
            $data['document_file'] = $path;
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('public');
            $data['document_cloud_url'] = $disk->url($path);
            $data['document_type'] = $request->document_type ?? $file->getClientOriginalExtension();
        }

        // Handle quiz content
        if ($request->content_type === 'quiz' && $request->quiz_id) {
            $data['quiz_id'] = $request->quiz_id;
        }

        $contentItem = ContentItem::create($data);

        return redirect()->back()->with('success', 'Content item created successfully.');
    }

    public function update(Request $request, $bookId, $chapterId, $lessonId, $contentItemId)
    {
        $book = Book::findOrFail($bookId);
        $contentItem = ContentItem::with('lesson.chapter')->findOrFail($contentItemId);

        // Check if teacher owns this course
        if ($book->teacher_id !== Auth::id() || $contentItem->lesson->chapter->book_id !== $bookId) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_index' => 'nullable|integer',
            'is_preview' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $contentItem->update([
            'title' => $request->title,
            'description' => $request->description,
            'order_index' => $request->order_index ?? $contentItem->order_index,
            'is_preview' => $request->has('is_preview') ? true : false,
            'is_active' => $request->has('is_active') ? $request->is_active : $contentItem->is_active,
        ]);

        return redirect()->back()->with('success', 'Content item updated successfully.');
    }

    public function destroy($bookId, $chapterId, $lessonId, $contentItemId)
    {
        $book = Book::findOrFail($bookId);
        $contentItem = ContentItem::with('lesson.chapter')->findOrFail($contentItemId);

        // Check if teacher owns this course
        if ($book->teacher_id !== Auth::id() || $contentItem->lesson->chapter->book_id !== $bookId) {
            abort(403, 'Unauthorized');
        }

        // Delete associated files
        if ($contentItem->video_file) {
            Storage::disk('public')->delete($contentItem->video_file);
        }
        if ($contentItem->document_file) {
            Storage::disk('public')->delete($contentItem->document_file);
        }

        $contentItem->delete();

        return redirect()->back()->with('success', 'Content item deleted successfully.');
    }
}
