<?php

namespace App\Http\Middleware;

use App\Models\Book;
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
        $bookId = $request->route('book') ?? $request->route('course');
        
        if ($bookId) {
            $book = Book::findOrFail($bookId);
            
            // Check if course is free
            if ($book->is_free) {
                return $next($request);
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
