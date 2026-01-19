<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CourseEnrollment;

class Chapter extends Model
{
    protected $fillable = [
        'book_id',
        'module_id',
        'title',
        'description',
        'chapter_type',
        'order',
        'is_preview',
        'is_free', // Keep for backward compatibility
        'is_active',
    ];

    protected $casts = [
        'is_preview' => 'boolean',
        'is_free' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    /**
     * Check if chapter is accessible (preview or user has paid access)
     */
    public function isAccessible($user = null): bool
    {
        // If preview, always accessible
        if ($this->is_preview || $this->is_free) {
            return true;
        }

        // If course is free, accessible
        if ($this->book && $this->book->is_free) {
            return true;
        }

        // Check if user is enrolled and has paid
        if ($user) {
            $enrollment = CourseEnrollment::where('user_id', $user->id)
                ->where('book_id', $this->book_id)
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
