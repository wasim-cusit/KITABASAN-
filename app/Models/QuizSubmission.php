<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'lesson_id',
        'answers',
        'total_questions',
        'correct_answers',
        'score',
        'passing_score',
        'passed',
        'time_taken',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'decimal:2',
        'passed' => 'boolean',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Check if submission passed (score >= passing_score)
     */
    public function isPassed(): bool
    {
        return $this->score >= $this->passing_score;
    }

    /**
     * Get the result message
     */
    public function getResultMessage(): string
    {
        if ($this->passed) {
            return "Congratulations! You passed with {$this->score}%";
        } else {
            $needed = $this->passing_score - $this->score;
            return "You scored {$this->score}%. You need {$needed}% more to pass (minimum {$this->passing_score}%)";
        }
    }
}
