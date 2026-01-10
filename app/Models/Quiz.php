<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'questions_json',
        'time_limit',
        'passing_score',
        'order',
        'is_active',
        'is_preview',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_preview' => 'boolean',
        'questions_json' => 'array',
        'passing_score' => 'integer',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function mcqs(): HasMany
    {
        return $this->hasMany(Mcq::class)->orderBy('order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(QuizSubmission::class)->orderBy('submitted_at', 'desc');
    }

    public function userSubmissions($userId): HasMany
    {
        return $this->hasMany(QuizSubmission::class)->where('user_id', $userId)->orderBy('submitted_at', 'desc');
    }

    /**
     * Check if user has passed this quiz
     */
    public function hasUserPassed($userId): bool
    {
        return $this->submissions()
            ->where('user_id', $userId)
            ->where('passed', true)
            ->exists();
    }

    /**
     * Get user's best score
     */
    public function getUserBestScore($userId): ?float
    {
        $submission = $this->submissions()
            ->where('user_id', $userId)
            ->orderBy('score', 'desc')
            ->first();

        return $submission ? (float) $submission->score : null;
    }

    /**
     * Get questions from questions_json or mcqs relationship
     */
    public function getQuestions(): array
    {
        if ($this->questions_json && is_array($this->questions_json)) {
            return $this->questions_json;
        }

        // Fallback to MCQs relationship
        return $this->mcqs->map(function ($mcq) {
            return [
                'id' => $mcq->id,
                'question' => $mcq->question,
                'options' => $mcq->options,
                'correct_answer' => $mcq->correct_answer,
                'explanation' => $mcq->explanation,
                'points' => $mcq->points ?? 1,
            ];
        })->toArray();
    }
}
