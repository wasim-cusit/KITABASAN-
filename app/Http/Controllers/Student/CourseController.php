<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Mail\NewEnrollmentMail;
use App\Models\Book;
use App\Models\CourseEnrollment;
use App\Models\TeacherProfile;
use App\Services\EmailConfigService;
use App\Services\StudentNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Book::where('status', 'published')
            ->with(['subject.grade', 'teacher'])
            ->latest()
            ->paginate(12);

        return view('student.courses.index', compact('courses'));
    }

    public function show($id)
    {
        $course = Book::with(['chapters.lessons.topics', 'subject.grade', 'teacher'])
            ->findOrFail($id);

        if ($course->status !== 'published') {
            abort(404);
        }

        $user = Auth::user();
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('book_id', $id)
            ->where('status', 'active')
            ->where(function($query) use ($course) {
                // For paid courses, require payment_status = 'paid'
                if (!$course->is_free) {
                    $query->where('payment_status', 'paid');
                }
            })
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();

        // Count preview and paid content (using is_preview flag)
        $previewChapters = $course->chapters->where('is_preview', true)->count();
        $paidChapters = $course->chapters->where('is_preview', false)->where('is_free', false)->count();
        $totalLessons = $course->chapters->sum(fn($ch) => $ch->lessons->count());
        $previewLessons = $course->chapters->sum(fn($ch) => $ch->lessons->where('is_preview', true)->count());
        $freeChapters = $course->chapters->where('is_free', true)->count(); // Keep for backward compatibility
        $freeLessons = $course->chapters->sum(fn($ch) => $ch->lessons->where('is_free', true)->count());

        return view('student.courses.show', compact('course', 'enrollment', 'freeChapters', 'paidChapters', 'totalLessons', 'freeLessons', 'previewChapters', 'previewLessons'));
    }

    public function enroll($id)
    {
        $course = Book::findOrFail($id);
        $user = Auth::user();

        if ($course->is_free) {
            // Free enrollment
            $enrollment = CourseEnrollment::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'book_id' => $course->id,
                ],
                [
                    'status' => 'active',
                    'payment_status' => 'free', // Set payment status to 'free' for free courses
                    'enrolled_at' => now(),
                    'expires_at' => ($course->access_duration_months ?? $course->duration_months) ? now()->addMonths($course->access_duration_months ?? $course->duration_months) : null, // null = lifetime access
                ]
            );

            // Notify teacher of new enrollment if they have email_notifications on
            if ($enrollment->wasRecentlyCreated) {
                try {
                    $enrollment->load(['book', 'user']);
                    $teacher = $course->teacher;
                    if ($teacher) {
                        $profile = TeacherProfile::where('user_id', $teacher->id)->first();
                        if ($profile->email_notifications ?? true) {
                            EmailConfigService::apply();
                            if (EmailConfigService::isConfigured()) {
                                Mail::to($teacher->email)->send(new NewEnrollmentMail($enrollment, $teacher));
                            }
                        }
                    }
                    StudentNotificationService::sendEnrollmentConfirmation($enrollment);
                } catch (\Throwable $e) {
                    Log::warning('NewEnrollmentMail not sent (free enroll): ' . $e->getMessage());
                }
            }

            return redirect()->route('student.learning.index', $course->id)
                ->with('success', 'Successfully enrolled in the course!');
        } else {
            // Paid course - redirect to payment page
            return redirect()->route('student.payments.index', ['course_id' => $course->id]);
        }
    }
}
