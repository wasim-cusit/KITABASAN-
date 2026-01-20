<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class VideoService
{
    /**
     * Get YouTube video details using YouTube Data API v3
     */
    public function getYouTubeVideoDetails(string $videoId, bool $checkPrivacy = false): array
    {
        $apiKey = config('services.youtube.api_key');

        if (!$apiKey) {
            return [];
        }

        try {
            $parts = ['snippet', 'contentDetails', 'statistics'];
            if ($checkPrivacy) {
                $parts[] = 'status'; // Includes privacyStatus
            }

            $response = Http::get('https://www.googleapis.com/youtube/v3/videos', [
                'id' => $videoId,
                'key' => $apiKey,
                'part' => implode(',', $parts),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['items']) && count($data['items']) > 0) {
                    return $data['items'][0];
                }
            }
        } catch (\Exception $e) {
            \Log::error('YouTube API Error: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Check if YouTube video is accessible (public or unlisted)
     * Note: Private videos require OAuth2 authentication to verify access
     */
    public function isYouTubeVideoAccessible(string $videoId): bool
    {
        $details = $this->getYouTubeVideoDetails($videoId, true);
        
        if (empty($details)) {
            return false;
        }

        $privacyStatus = $details['status']['privacyStatus'] ?? 'private';
        
        // Public and unlisted videos are accessible
        return in_array($privacyStatus, ['public', 'unlisted']);
    }

    /**
     * Extract YouTube video ID from URL or return as-is if already an ID
     */
    public function extractYouTubeId($input): string
    {
        if (empty($input)) {
            return '';
        }

        // If it's already just an ID (11 characters, alphanumeric, dashes, underscores)
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) {
            return $input;
        }

        // Try to extract from various YouTube URL formats
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/',
            '/youtu\.be\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/v\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/.*[?&]v=([a-zA-Z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input, $matches)) {
                return $matches[1];
            }
        }

        // If no pattern matches, return original (might be invalid, but let validation handle it)
        return $input;
    }

    /**
     * Get YouTube embed URL with privacy controls for unlisted/private videos
     */
    public function getYouTubeEmbedUrl(string $videoId, string $privacy = 'public', array $options = []): string
    {
        // Extract video ID if a full URL was provided
        $cleanVideoId = $this->extractYouTubeId($videoId);
        
        $embedUrl = "https://www.youtube.com/embed/{$cleanVideoId}";
        
        $params = array_merge([
            'enablejsapi' => 1,
            'origin' => request()->getHost(),
            'rel' => 0, // Don't show related videos
            'modestbranding' => 1, // Minimal YouTube branding
        ], $options);

        // For unlisted/private videos, add additional parameters
        if ($privacy === 'unlisted' || $privacy === 'private') {
            $params['autoplay'] = 0;
            $params['controls'] = 1;
        }

        return $embedUrl . '?' . http_build_query($params);
    }

    /**
     * Validate YouTube video ID and privacy status
     */
    public function validateYouTubeVideo(string $videoId, string $expectedPrivacy = null): array
    {
        $details = $this->getYouTubeVideoDetails($videoId, true);
        
        if (empty($details)) {
            return [
                'valid' => false,
                'error' => 'Video not found or API key invalid',
            ];
        }

        $privacyStatus = $details['status']['privacyStatus'] ?? 'unknown';
        $snippet = $details['snippet'] ?? [];
        $contentDetails = $details['contentDetails'] ?? [];

        $result = [
            'valid' => true,
            'video_id' => $videoId,
            'title' => $snippet['title'] ?? '',
            'description' => $snippet['description'] ?? '',
            'thumbnail' => $snippet['thumbnails']['high']['url'] ?? '',
            'privacy_status' => $privacyStatus,
            'duration' => $this->parseYouTubeDuration($contentDetails['duration'] ?? 'PT0S'),
            'published_at' => $snippet['publishedAt'] ?? null,
        ];

        // Check if privacy matches expected
        if ($expectedPrivacy && $privacyStatus !== $expectedPrivacy) {
            $result['warning'] = "Video privacy is '{$privacyStatus}', expected '{$expectedPrivacy}'";
        }

        // Check accessibility
        $result['accessible'] = $this->isYouTubeVideoAccessible($videoId);

        return $result;
    }

    /**
     * Parse YouTube duration format (PT1H2M10S) to seconds
     */
    protected function parseYouTubeDuration(string $duration): int
    {
        preg_match('/PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?/', $duration, $matches);
        
        $hours = isset($matches[1]) ? (int) $matches[1] : 0;
        $minutes = isset($matches[2]) ? (int) $matches[2] : 0;
        $seconds = isset($matches[3]) ? (int) $matches[3] : 0;

        return ($hours * 3600) + ($minutes * 60) + $seconds;
    }

    /**
     * Upload video to cloud storage (S3, Cloudinary, etc.)
     */
    public function uploadVideoToCloud($file, string $disk = 's3'): array
    {
        try {
            $path = 'videos/' . date('Y/m') . '/' . uniqid() . '_' . $file->getClientOriginalName();
            
            $uploadedPath = \Storage::disk($disk)->putFileAs(
                dirname($path),
                $file,
                basename($path),
                'public'
            );

            $url = \Storage::disk($disk)->url($uploadedPath);

            return [
                'success' => true,
                'path' => $uploadedPath,
                'url' => $url,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ];
        } catch (\Exception $e) {
            \Log::error('Video upload error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
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

