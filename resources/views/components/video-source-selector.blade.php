@props(['lesson', 'type' => 'lesson'])

<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Video Source</label>
        <select name="video_host" id="videoHost" class="w-full px-3 py-2 border rounded" onchange="toggleVideoInputs()">
            <option value="">Select video source</option>
            <option value="youtube" {{ old('video_host', $lesson->video_host ?? '') === 'youtube' ? 'selected' : '' }}>YouTube</option>
            <option value="bunny" {{ old('video_host', $lesson->video_host ?? '') === 'bunny' ? 'selected' : '' }}>Bunny Stream</option>
            <option value="upload" {{ old('video_host', $lesson->video_host ?? '') === 'upload' ? 'selected' : '' }}>Upload Video</option>
        </select>
    </div>

    <!-- YouTube Input -->
    <div id="youtubeInput" class="hidden">
        <label class="block text-sm font-medium text-gray-700 mb-1">YouTube Video ID</label>
        <input type="text" name="video_id" value="{{ old('video_id', $lesson->video_id ?? '') }}"
               placeholder="e.g., dQw4w9WgXcQ" class="w-full px-3 py-2 border rounded">
        <p class="text-xs text-gray-500 mt-1">Enter the YouTube video ID from the URL</p>
    </div>

    <!-- Bunny Stream Input -->
    <div id="bunnyInput" class="hidden">
        <label class="block text-sm font-medium text-gray-700 mb-1">Bunny Stream Video ID</label>
        <input type="text" name="video_id" value="{{ old('video_id', $lesson->video_id ?? '') }}"
               placeholder="Enter Bunny Stream video ID" class="w-full px-3 py-2 border rounded">
    </div>

    <!-- Upload Video Input -->
    <div id="uploadInput" class="hidden">
        <label class="block text-sm font-medium text-gray-700 mb-1">Upload Video File</label>
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
            @if(isset($lesson) && $lesson->video_file)
                <div class="mb-2">
                    <p class="text-sm text-gray-600">Current video: {{ basename($lesson->video_file) }}</p>
                    <a href="{{ route('teacher.videos.delete', ['type' => $type, 'id' => $lesson->id]) }}"
                       class="text-red-600 text-sm hover:text-red-700"
                       onclick="return confirm('Are you sure you want to delete this video?')">Delete current video</a>
                </div>
            @endif
            <input type="file" name="video_file" accept="video/*" class="w-full">
            <p class="text-xs text-gray-500 mt-1">Supported: MP4, AVI, MOV, WMV, FLV, WebM (Max 100GB)</p>
            <a href="{{ isset($lesson) ? route('teacher.lessons.upload-video', $lesson->id) : '#' }}"
               class="text-blue-600 text-sm hover:text-blue-700 mt-2 inline-block">
                Or use advanced upload page →
            </a>
        </div>
    </div>

    <!-- Video Preview -->
    @if(isset($lesson) && $lesson->video_host)
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
            @if($lesson->video_host === 'youtube')
                <iframe src="https://www.youtube.com/embed/{{ $lesson->video_id }}"
                        class="w-full h-64 rounded" frameborder="0" allowfullscreen></iframe>
            @elseif($lesson->video_host === 'upload' && $lesson->video_file)
                <video controls class="w-full h-64 rounded">
                    <source src="{{ route('storage.serve', ['path' => ltrim(str_replace('\\', '/', $lesson->video_file), '/')]) }}" type="{{ $lesson->video_mime_type }}">
                </video>
            @endif
        </div>
    @endif
</div>

<script>
function toggleVideoInputs() {
    const host = document.getElementById('videoHost').value;
    document.getElementById('youtubeInput').classList.add('hidden');
    document.getElementById('bunnyInput').classList.add('hidden');
    document.getElementById('uploadInput').classList.add('hidden');

    if (host === 'youtube') {
        document.getElementById('youtubeInput').classList.remove('hidden');
    } else if (host === 'bunny') {
        document.getElementById('bunnyInput').classList.remove('hidden');
    } else if (host === 'upload') {
        document.getElementById('uploadInput').classList.remove('hidden');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleVideoInputs();
});
</script>

