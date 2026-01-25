<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CourseEnrollment;

class Lesson extends Model
{
    protected $fillable = [
        'chapter_id',
        'title',
        'description',
        'video_id',
        'video_host',
        'video_file',
        'video_size',
        'video_mime_type',
        'duration',
        'order',
        'status',
        'is_free', // Keep for backward compatibility
        'is_preview',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_preview' => 'boolean',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class)->orderBy('order');
    }

    public function contentItems(): HasMany
    {
        return $this->hasMany(ContentItem::class)->orderBy('order_index');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(ContentItem::class)->where('content_type', 'video')->orderBy('order_index');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ContentItem::class)->where('content_type', 'document')->orderBy('order_index');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * Check if lesson is accessible (preview or user has paid access)
     */
    public function isAccessible($user = null): bool
    {
        // If preview, always accessible
        if ($this->is_preview || $this->is_free) {
            return true;
        }

        // If course is free, accessible
        if ($this->chapter && $this->chapter->book && $this->chapter->book->is_free) {
            return true;
        }

        // Check if user is enrolled and has paid
        if ($user && $this->chapter && $this->chapter->book) {
            $enrollment = CourseEnrollment::where('user_id', $user->id)
                ->where('book_id', $this->chapter->book_id)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->where('expires_at', '>', now())
                        ->orWhereNull('expires_at');
                })
                ->where('payment_status', 'paid')
                ->first();

            return $enrollment !== null;
        }

        return false;
    }
}
