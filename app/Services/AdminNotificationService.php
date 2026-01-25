<?php

namespace App\Services;

use App\Mail\AdminCourseUpdateMail;
use App\Mail\AdminNewCourseMail;
use App\Mail\AdminNewStudentMail;
use App\Mail\AdminNewTeacherMail;
use App\Models\AdminNotificationSetting;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminNotificationService
{
    /**
     * Get admins who have the given setting enabled. If no rows, treat as enabled.
     */
    protected static function adminsWithSetting(string $settingKey): \Illuminate\Database\Eloquent\Collection
    {
        $admins = User::role('admin')->get();
        if ($admins->isEmpty()) {
            return $admins;
        }
        $prefs = AdminNotificationSetting::whereIn('user_id', $admins->pluck('id'))
            ->get()
            ->keyBy('user_id');
        return $admins->filter(function ($admin) use ($prefs, $settingKey) {
            $p = $prefs->get($admin->id);
            return $p ? (bool) ($p->{$settingKey} ?? true) : true;
        });
    }

    public static function notifyNewStudent(User $student, string $source = 'registration'): void
    {
        try {
            $admins = self::adminsWithSetting('email_new_students');
            foreach ($admins as $admin) {
                if ($admin->email) {
                    EmailConfigService::apply();
                    if (EmailConfigService::isConfigured()) {
                        Mail::to($admin->email)->send(new AdminNewStudentMail($student, $source));
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('AdminNewStudentMail not sent: ' . $e->getMessage());
        }
    }

    public static function notifyNewTeacher(User $teacher): void
    {
        try {
            $admins = self::adminsWithSetting('email_new_teachers');
            foreach ($admins as $admin) {
                if ($admin->email) {
                    EmailConfigService::apply();
                    if (EmailConfigService::isConfigured()) {
                        Mail::to($admin->email)->send(new AdminNewTeacherMail($teacher));
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('AdminNewTeacherMail not sent: ' . $e->getMessage());
        }
    }

    public static function notifyNewCourse(Book $course, string $source = 'teacher'): void
    {
        try {
            $course->load('teacher');
            $admins = self::adminsWithSetting('email_new_courses');
            foreach ($admins as $admin) {
                if ($admin->email) {
                    EmailConfigService::apply();
                    if (EmailConfigService::isConfigured()) {
                        Mail::to($admin->email)->send(new AdminNewCourseMail($course, $source));
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('AdminNewCourseMail not sent: ' . $e->getMessage());
        }
    }

    public static function notifyCourseUpdate(Book $course, string $changeSummary): void
    {
        try {
            $course->load('teacher');
            $admins = self::adminsWithSetting('email_course_updates');
            foreach ($admins as $admin) {
                if ($admin->email) {
                    EmailConfigService::apply();
                    if (EmailConfigService::isConfigured()) {
                        Mail::to($admin->email)->send(new AdminCourseUpdateMail($course, $changeSummary));
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('AdminCourseUpdateMail not sent: ' . $e->getMessage());
        }
    }
}
