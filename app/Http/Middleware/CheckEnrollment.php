<?php

namespace App\Http\Middleware;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Topic;
use App\Models\ContentItem;
use App\Models\CourseEnrollment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEnrollment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        CourseEnrollment::expireForUser($user->id);
        $bookId = $request->route('book') ?? $request->route('course');
        
        if ($bookId) {
            $book = Book::findOrFail($bookId);
            
            // Check if course is free
            if ($book->is_free) {
                return $next($request);
            }

            // Allow access to free/preview content without enrollment
            $chapterId = $request->route('chapter');
            $lessonId = $request->route('lesson');
            $topicId = $request->route('topic');
            $contentItemId = $request->route('contentItem') ?? $request->route('content_item');

            if ($chapterId) {
                $chapter = Chapter::find($chapterId);
                if ($chapter && ($chapter->is_free || $chapter->is_preview)) {
                    return $next($request);
                }
            }

            if ($lessonId) {
                $lesson = Lesson::find($lessonId);
                if ($lesson && ($lesson->is_free || $lesson->is_preview)) {
                    return $next($request);
                }
            }

            if ($topicId) {
                $topic = Topic::with('lesson')->find($topicId);
                if ($topic && ($topic->is_free || ($topic->lesson && ($topic->lesson->is_free || $topic->lesson->is_preview)))) {
                    return $next($request);
                }
            }

            if ($contentItemId) {
                $contentItem = ContentItem::with('lesson')->find($contentItemId);
                if ($contentItem && ($contentItem->is_preview || ($contentItem->lesson && ($contentItem->lesson->is_free || $contentItem->lesson->is_preview)))) {
                    return $next($request);
                }
            }

            // Check if user is enrolled and enrollment is active with paid status
            $enrollment = CourseEnrollment::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->where('status', 'active')
                ->where('payment_status', 'paid') // Must be paid, not just enrolled
                ->where(function ($query) {
                    $query->where('expires_at', '>', now())
                        ->orWhereNull('expires_at');
                })
                ->first();

            if (!$enrollment) {
                return redirect()->route('student.courses.show', $book->id)
                    ->with('error', 'You need to enroll and pay for this course to access the content.');
            }
        }

        return $next($request);
    }
}
