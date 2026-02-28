<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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
        'email_notifications',
        'course_updates',
        'show_profile',
        'show_email',
    ];

    protected $casts = [
        'social_links' => 'array',
        'rating' => 'decimal:2',
        'email_notifications' => 'boolean',
        'course_updates' => 'boolean',
        'show_profile' => 'boolean',
        'show_email' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if profile image exists on disk.
     */
    public function hasValidProfileImage(): bool
    {
        return $this->profile_image && Storage::disk('public')->exists($this->profile_image);
    }

    /**
     * Check if cover image exists on disk.
     */
    public function hasValidCoverImage(): bool
    {
        return $this->cover_image && Storage::disk('public')->exists($this->cover_image);
    }

    /**
     * Get profile image URL when path is set. Uses storage.serve route so Laravel always serves it.
     */
    public function getProfileImageUrl(): ?string
    {
        if (empty($this->profile_image)) {
            return null;
        }
        $path = ltrim(str_replace('\\', '/', $this->profile_image), '/');
        return route('storage.serve', ['path' => $path]);
    }

    /**
     * Get cover image URL when path is set. Uses storage.serve route so Laravel always serves it.
     */
    public function getCoverImageUrl(): ?string
    {
        if (empty($this->cover_image)) {
            return null;
        }
        $path = ltrim(str_replace('\\', '/', $this->cover_image), '/');
        return route('storage.serve', ['path' => $path]);
    }
}
