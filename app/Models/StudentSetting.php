<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSetting extends Model
{
    protected $fillable = [
        'user_id',
        'email_enrollment_confirmation',
        'email_course_updates',
    ];

    protected $casts = [
        'email_enrollment_confirmation' => 'boolean',
        'email_course_updates' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
