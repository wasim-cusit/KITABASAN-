<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'email_new_students',
        'email_new_teachers',
        'email_new_courses',
        'email_course_updates',
        'email_device_bindings',
        'email_device_reset_requests',
    ];

    protected $casts = [
        'email_new_students' => 'boolean',
        'email_new_teachers' => 'boolean',
        'email_new_courses' => 'boolean',
        'email_course_updates' => 'boolean',
        'email_device_bindings' => 'boolean',
        'email_device_reset_requests' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
