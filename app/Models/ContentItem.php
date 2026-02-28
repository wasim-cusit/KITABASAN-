<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ContentItem extends Model
{
    protected $fillable = [
        'lesson_id',
        'content_type',
        'title',
        'description',
        'video_id',
        'video_provider',
        'youtube_privacy',
        'video_file',
        'video_cloud_url',
        'duration',
        'quiz_id',
        'document_file',
        'document_cloud_url',
        'document_type',
        'audio_file',
        'audio_cloud_url',
        'text_content',
        'image_file',
        'image_cloud_url',
        'order_index',
        'is_preview',
        'is_active',
    ];

    protected $casts = [
        'is_preview' => 'boolean',
        'is_active' => 'boolean',
        'duration' => 'integer',
        'order_index' => 'integer',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the video URL based on provider
     */
    public function getVideoUrlAttribute(): ?string
    {
        if ($this->content_type !== 'video') {
            return null;
        }

        switch ($this->video_provider) {
            case 'youtube':
                return "https://www.youtube.com/embed/{$this->video_id}";
            case 'upload':
                if ($this->video_cloud_url) {
                    return $this->video_cloud_url;
                }
                if (!$this->video_file) {
                    return null;
                }
                /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
                $disk = Storage::disk('public');
                return $disk->url($this->video_file);
            case 'bunny':
                return $this->video_cloud_url;
            default:
                return null;
        }
    }

    /**
     * Get the document URL
     */
    public function getDocumentUrlAttribute(): ?string
    {
        if ($this->content_type !== 'document') {
            return null;
        }

        if ($this->document_cloud_url) {
            return $this->document_cloud_url;
        }
        if (!$this->document_file) {
            return null;
        }
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        return $disk->url($this->document_file);
    }
}
