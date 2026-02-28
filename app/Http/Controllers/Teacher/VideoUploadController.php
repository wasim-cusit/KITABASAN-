<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoUploadController extends Controller
{
    /**
     * Show upload form for lesson video
     */
    public function showUploadForm($lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);

        // Check authorization
        if ($lesson->chapter->book->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $uploadUrl = route('teacher.lessons.upload-video.post', $lessonId);

        return view('teacher.videos.upload', compact('lesson', 'uploadUrl'));
    }

    /**
     * Upload video for a lesson
     */
    public function uploadLessonVideo(Request $request, $lessonId)
    {
        $request->validate([
            'video' => 'required|mimes:mp4,avi,mov,wmv,flv,webm|max:102400', // Max 100GB (adjust as needed)
        ]);

        $lesson = Lesson::findOrFail($lessonId);

        // Check authorization
        if ($lesson->chapter->book->teacher_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $filename = Str::uuid() . '.' . $video->getClientOriginalExtension();
            $path = $video->storeAs('videos/lessons', $filename, 'public');

            // Get video duration (you may need to install ffmpeg for this)
            $duration = $this->getVideoDuration($video);

            // Delete old video if exists
            if ($lesson->video_file) {
                Storage::disk('public')->delete($lesson->video_file);
            }

            $lesson->update([
                'video_file' => $path,
                'video_host' => 'upload',
                'video_size' => $video->getSize(),
                'video_mime_type' => $video->getMimeType(),
                'duration' => $duration,
                'video_id' => null, // Clear external video ID
            ]);

            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('public');

            return response()->json([
                'success' => true,
                'message' => 'Video uploaded successfully',
                'video_path' => $disk->url($path),
                'duration' => $duration,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No video file provided',
        ], 400);
    }

    /**
     * Upload video for a topic
     */
    public function uploadTopicVideo(Request $request, $topicId)
    {
        $request->validate([
            'video' => 'required|mimes:mp4,avi,mov,wmv,flv,webm|max:102400', // Max 100GB
        ]);

        $topic = Topic::findOrFail($topicId);

        // Check authorization
        if ($topic->lesson->chapter->book->teacher_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $filename = Str::uuid() . '.' . $video->getClientOriginalExtension();
            $path = $video->storeAs('videos/topics', $filename, 'public');

            // Get video duration
            $duration = $this->getVideoDuration($video);

            // Delete old video if exists
            if ($topic->video_file) {
                Storage::disk('public')->delete($topic->video_file);
            }

            $topic->update([
                'video_file' => $path,
                'video_host' => 'upload',
                'video_size' => $video->getSize(),
                'video_mime_type' => $video->getMimeType(),
                'duration' => $duration,
                'video_id' => null, // Clear external video ID
            ]);

            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('public');

            return response()->json([
                'success' => true,
                'message' => 'Video uploaded successfully',
                'video_path' => $disk->url($path),
                'duration' => $duration,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No video file provided',
        ], 400);
    }

    /**
     * Delete uploaded video
     */
    public function deleteVideo(Request $request, $type, $id)
    {
        if ($type === 'lesson') {
            $item = Lesson::findOrFail($id);
        } elseif ($type === 'topic') {
            $item = Topic::findOrFail($id);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
        }

        // Check authorization
        if ($type === 'lesson') {
            if ($item->chapter->book->teacher_id !== Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        } else {
            if ($item->lesson->chapter->book->teacher_id !== Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }

        if ($item->video_file) {
            Storage::disk('public')->delete($item->video_file);
            $item->update([
                'video_file' => null,
                'video_host' => null,
                'video_size' => null,
                'video_mime_type' => null,
                'duration' => 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Video deleted successfully',
        ]);
    }

    /**
     * Get video duration (requires ffmpeg or use a library)
     * For now, return 0 - you can implement proper duration detection later
     */
    private function getVideoDuration($videoFile)
    {
        // TODO: Implement video duration detection using ffmpeg or a PHP library
        // For now, return 0 or estimate based on file size
        // Example with getID3 library:
        // $getID3 = new \getID3;
        // $fileInfo = $getID3->analyze($videoFile->getRealPath());
        // return isset($fileInfo['playtime_seconds']) ? (int)$fileInfo['playtime_seconds'] : 0;

        return 0; // Placeholder
    }
}
