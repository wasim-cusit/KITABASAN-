<?php

namespace App\Services;

use App\Mail\CourseUpdateMail;
use App\Models\Book;
use App\Models\TeacherProfile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CourseNotificationService
{
    /**
     * Notify the course teacher(s) of an update, if they have course_updates enabled.
     */
    public static function notifyCourseUpdate(Book $course, string $changeSummary): void
    {
        try {
            $teacher = $course->teacher;
            if (!$teacher) {
                return;
            }
            $profile = TeacherProfile::where('user_id', $teacher->id)->first();
            if (!($profile->course_updates ?? true)) {
                return;
            }
            EmailConfigService::apply();
            if (EmailConfigService::isConfigured()) {
                Mail::to($teacher->email)->send(new CourseUpdateMail($course, $teacher, $changeSummary));
            }
        } catch (\Throwable $e) {
            Log::warning('CourseUpdateMail not sent: ' . $e->getMessage());
        }
    }
}
