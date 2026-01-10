<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    protected $fillable = [
        'subject_id',
        'grade_name',
        'subject_name',
        'teacher_id',
        'title',
        'slug',
        'description',
        'short_description',
        'thumbnail',
        'cover_image',
        'price',
        'is_free',
        'duration_months',
        'access_duration_months',
        'status',
        'order',
        'total_lessons',
        'total_duration',
        'enrollment_count',
        'rating',
        'rating_count',
        // New fields
        'language',
        'difficulty_level',
        'course_level',
        'learning_objectives',
        'prerequisites',
        'tags',
        'max_enrollments',
        'start_date',
        'end_date',
        'certificate_enabled',
        'reviews_enabled',
        'comments_enabled',
        'intro_video_url',
        'intro_video_provider',
        'what_you_will_learn',
        'course_requirements',
        'target_audience',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_featured',
        'is_popular',
        'priority_order',
        'duration_hours',
        'lectures_count',
        'resources_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_free' => 'boolean',
        'rating' => 'decimal:2',
        'learning_objectives' => 'array',
        'tags' => 'array',
        'meta_keywords' => 'array',
        'certificate_enabled' => 'boolean',
        'reviews_enabled' => 'boolean',
        'comments_enabled' => 'boolean',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order_index');
    }

    public function activeModules(): HasMany
    {
        return $this->hasMany(Module::class)->where('is_active', true)->orderBy('order_index');
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_teachers')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Check if user is a teacher of this course (either as creator or co-teacher)
     */
    public function hasTeacher($userId): bool
    {
        // Check if user is the main teacher
        if ($this->teacher_id == $userId) {
            return true;
        }

        // Check if user is in the teachers pivot table
        return $this->teachers()->where('users.id', $userId)->exists();
    }

    /**
     * Get all teachers (including main teacher)
     */
    public function getAllTeachers()
    {
        $teachers = collect();

        // Add main teacher
        if ($this->teacher) {
            $teachers->push($this->teacher);
        }

        // Add co-teachers from pivot table
        $coTeachers = $this->teachers()->where('course_teachers.role', 'co-teacher')->get();
        $teachers = $teachers->merge($coTeachers);

        return $teachers->unique('id');
    }
}
