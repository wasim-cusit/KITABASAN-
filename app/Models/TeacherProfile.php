<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherProfile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'qualifications',
        'specializations',
        'profile_image',
        'cover_image',
        'social_links',
        'total_courses',
        'total_students',
        'rating',
        'rating_count',
        'status',
    ];

    protected $casts = [
        'social_links' => 'array',
        'rating' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
