<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    protected $fillable = [
        'book_id',
        'title',
        'description',
        'order_index',
        'is_active',
        'release_type',
        'release_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_index' => 'integer',
        'release_date' => 'datetime',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    public function activeChapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->where('is_active', true)->orderBy('order');
    }
}
