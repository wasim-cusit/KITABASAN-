<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CourseEnrollment;

class Topic extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'video_id',
        'video_host',
        'video_file',
        'video_size',
        'video_mime_type',
        'duration',
        'type',
        'order',
        'is_free',
        'is_preview',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_preview' => 'boolean',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Check if topic is accessible (preview or user has paid access)
     */
    public function isAccessible($user = null): bool
    {
        if ($this->is_preview || $this->is_free) {
            return true;
        }

        if ($this->lesson && $this->lesson->chapter && $this->lesson->chapter->book && $this->lesson->chapter->book->is_free) {
            return true;
        }

        if ($user && $this->lesson && $this->lesson->chapter && $this->lesson->chapter->book) {
            $enrollment = CourseEnrollment::where('user_id', $user->id)
                ->where('book_id', $this->lesson->chapter->book_id)
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
