<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class VideoService
{
    /**
     * Get YouTube video embed URL
     */
    public function getYouTubeEmbedUrl(string $videoId): string
    {
        return "https://www.youtube.com/embed/{$videoId}";
    }

    /**
     * Get YouTube video details
     */
    public function getYouTubeVideoDetails(string $videoId): array
    {
        $apiKey = config('services.youtube.api_key');

        if (!$apiKey) {
            return [];
        }

        try {
            $response = Http::get('https://www.googleapis.com/youtube/v3/videos', [
                'id' => $videoId,
                'key' => $apiKey,
                'part' => 'snippet,contentDetails,statistics',
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            \Log::error('YouTube API Error: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Get Bunny Stream video URL
     */
    public function getBunnyStreamUrl(string $videoId, string $libraryId): string
    {
        $apiKey = config('services.bunny.api_key');
        $cdnHostname = config('services.bunny.cdn_hostname');

        // Generate secure token if needed
        // TODO: Implement Bunny Stream secure token generation

        return "https://{$cdnHostname}/{$videoId}/play_480p.mp4";
    }

    /**
     * Get uploaded video URL
     */
    public function getUploadedVideoUrl(string $videoPath): string
    {
        return \Storage::disk('public')->url($videoPath);
    }

    /**
     * Generate secure embed code
     */
    public function generateSecureEmbed(string $videoId, string $host, array $options = []): string
    {
        $allowedDomains = config('app.allowed_video_domains', []);
        $currentDomain = request()->getHost();

        if ($host === 'upload') {
            // For uploaded videos, return the video URL directly
            return $this->getUploadedVideoUrl($videoId);
        }

        if (!in_array($currentDomain, $allowedDomains)) {
            throw new \Exception('Domain not allowed for video embedding');
        }

        if ($host === 'youtube') {
            $embedUrl = $this->getYouTubeEmbedUrl($videoId);
            $params = http_build_query(array_merge([
                'enablejsapi' => 1,
                'origin' => $currentDomain,
            ], $options));

            return $embedUrl . '?' . $params;
        }

        if ($host === 'bunny') {
            return $this->getBunnyStreamUrl($videoId, $options['library_id'] ?? '');
        }

        return '';
    }

    /**
     * Get video player URL based on host type
     */
    public function getVideoPlayerUrl($lessonOrTopic): string
    {
        if (!$lessonOrTopic->video_host) {
            return '';
        }

        switch ($lessonOrTopic->video_host) {
            case 'youtube':
                return $this->getYouTubeEmbedUrl($lessonOrTopic->video_id);

            case 'bunny':
                return $this->getBunnyStreamUrl($lessonOrTopic->video_id, $options['library_id'] ?? '');

            case 'upload':
                return $this->getUploadedVideoUrl($lessonOrTopic->video_file);

            default:
                return '';
        }
    }

    /**
     * Track video play event
     */
    public function trackPlayEvent(int $userId, int $lessonId, int $position): void
    {
        // Track video play event for analytics
        // TODO: Implement video play tracking
    }
}

