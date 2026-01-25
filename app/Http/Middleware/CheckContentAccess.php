<?php

namespace App\Http\Middleware;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\ContentItem;
use App\Models\Topic;
use App\Models\CourseEnrollment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if user can access specific content (lesson/chapter/content item)
 * Supports flexible preview system with is_preview flag
 */
class CheckContentAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Try to get the content item from route
        $contentItemId = $request->route('contentItem') ?? $request->route('content_item');
        $lessonId = $request->route('lesson') ?? $request->route('lessonId');
        $topicId = $request->route('topic') ?? $request->route('topicId');
        $chapterId = $request->route('chapter') ?? $request->route('chapterId');
        $bookId = $request->route('book') ?? $request->route('bookId') ?? $request->route('course');
        $course = null;

        // Check content item access
        if ($contentItemId) {
            $contentItem = ContentItem::with(['lesson.chapter.book'])->findOrFail($contentItemId);
            
            if ($this->canAccessContentItem($contentItem, $user)) {
                return $next($request);
            }
            
            $course = $contentItem->lesson->chapter->book ?? null;
        }
        // Check topic access
        elseif ($topicId) {
            $topic = Topic::with(['lesson.chapter.book'])->findOrFail($topicId);
            
            if ($topic->isAccessible($user)) {
                return $next($request);
            }
            
            $course = $topic->lesson->chapter->book ?? null;
        }
        // Check lesson access
        elseif ($lessonId) {
            $lesson = Lesson::with(['chapter.book'])->findOrFail($lessonId);
            
            if ($lesson->isAccessible($user)) {
                return $next($request);
            }
            
            $course = $lesson->chapter->book ?? null;
        }
        // Check chapter access
        elseif ($chapterId) {
            $chapter = Chapter::with('book')->findOrFail($chapterId);
            
            if ($chapter->isAccessible($user)) {
                return $next($request);
            }
            
            $course = $chapter->book;
        }
        // Check course access (fallback)
        elseif ($bookId) {
            $course = Book::findOrFail($bookId);
            
            if ($course->is_free) {
                return $next($request);
            }

            if ($request->route() && $request->route()->getName() === 'student.learning.index') {
                return $next($request);
            }
            
            $enrollment = CourseEnrollment::where('user_id', $user->id)
                ->where('book_id', $course->id)
                ->where('status', 'active')
                ->where(function ($query) use ($course) {
                    if (!$course->is_free) {
                        $query->where('payment_status', 'paid');
                    }
                })
                ->where(function ($query) {
                    $query->where('expires_at', '>', now())
                        ->orWhereNull('expires_at');
                })
                ->first();

            if ($enrollment) {
                return $next($request);
            }
        }

        // No access - redirect with error
        if ($course) {
            return redirect()->route('student.courses.show', $course->id)
                ->with('error', 'This content requires course enrollment. Please enroll to continue.');
        }

        return redirect()->route('student.courses.index')
            ->with('error', 'Access denied. Please enroll in the course to view this content.');
    }

    /**
     * Check if user can access a content item
     */
    protected function canAccessContentItem(ContentItem $contentItem, $user): bool
    {
        // If preview, always accessible
        if ($contentItem->is_preview) {
            return true;
        }

        // Check parent lesson
        if ($contentItem->lesson && $contentItem->lesson->isAccessible($user)) {
            return true;
        }

        // Check if user has paid enrollment
        if ($contentItem->lesson && $contentItem->lesson->chapter && $contentItem->lesson->chapter->book) {
            $course = $contentItem->lesson->chapter->book;
            
            if ($course->is_free) {
                return true;
            }

            $enrollment = CourseEnrollment::where('user_id', $user->id)
                ->where('book_id', $course->id)
                ->where('status', 'active')
                ->where(function ($query) use ($course) {
                    if (!$course->is_free) {
                        $query->where('payment_status', 'paid');
                    }
                })
                ->where(function ($query) {
                    $query->where('expires_at', '>', now())
                        ->orWhereNull('expires_at');
                })
                ->first();

            return $enrollment !== null;
        }

        return false;
    }
}
