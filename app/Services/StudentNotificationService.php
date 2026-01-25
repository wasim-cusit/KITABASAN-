<?php

namespace App\Services;

use App\Mail\EnrollmentConfirmationMail;
use App\Mail\StudentCourseUpdateMail;
use App\Models\CourseEnrollment;
use App\Models\StudentSetting;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StudentNotificationService
{
    /**
     * Send enrollment confirmation to the student if they have it enabled.
     */
    public static function sendEnrollmentConfirmation(CourseEnrollment $enrollment): void
    {
        try {
            $student = $enrollment->user;
            if (!$student || !$student->email) {
                return;
            }
            $setting = StudentSetting::where('user_id', $student->id)->first();
            if (!($setting->email_enrollment_confirmation ?? true)) {
                return;
            }
            $enrollment->load(['book', 'user']);
            EmailConfigService::apply();
            if (EmailConfigService::isConfigured()) {
                Mail::to($student->email)->send(new EnrollmentConfirmationMail($enrollment, $student));
            }
        } catch (\Throwable $e) {
            Log::warning('EnrollmentConfirmationMail not sent: ' . $e->getMessage());
        }
    }

    /**
     * Notify enrolled students of a course update if they have it enabled.
     */
    public static function notifyCourseUpdate(\App\Models\Book $course, string $changeSummary): void
    {
        try {
            $enrollments = $course->enrollments()
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->with('user')
                ->get();

            foreach ($enrollments as $enrollment) {
                $student = $enrollment->user;
                if (!$student || !$student->email) {
                    continue;
                }
                $setting = StudentSetting::where('user_id', $student->id)->first();
                if (!($setting->email_course_updates ?? true)) {
                    continue;
                }
                EmailConfigService::apply();
                if (EmailConfigService::isConfigured()) {
                    Mail::to($student->email)->send(new StudentCourseUpdateMail($course, $student, $changeSummary));
                }
            }
        } catch (\Throwable $e) {
            Log::warning('StudentCourseUpdateMail not sent: ' . $e->getMessage());
        }
    }
}
