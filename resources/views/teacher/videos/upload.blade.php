@extends('layouts.teacher')

@section('title', 'Upload Video')
@section('page-title', 'Upload Video')

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    <div class="bg-white rounded-lg shadow p-4 lg:p-6">
        <h1 class="text-xl lg:text-2xl font-bold mb-6">Upload Video</h1>

        <div class="mb-6">
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center" id="dropZone">
                <form id="videoUploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="file" id="videoFile" name="video" accept="video/*" class="hidden" required>
                    <div class="space-y-4">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div>
                            <label for="videoFile" class="cursor-pointer">
                                <span class="text-blue-600 hover:text-blue-700 font-medium">Click to upload</span>
                                <span class="text-gray-600"> or drag and drop</span>
                            </label>
                            <p class="text-sm text-gray-500 mt-2">MP4, AVI, MOV, WMV, FLV, WebM (Max 100GB)</p>
                        </div>
                        <div id="fileName" class="hidden text-sm text-gray-600"></div>
                        <div id="fileSize" class="hidden text-sm text-gray-500"></div>
                    </div>
                </form>
            </div>
        </div>

        <div id="uploadProgress" class="hidden mb-4">
            <div class="bg-gray-200 rounded-full h-2">
                <div id="progressBar" class="bg-blue-600 h-2 rounded-full" style="width: 0%"></div>
            </div>
            <p id="progressText" class="text-sm text-gray-600 mt-2">Uploading...</p>
        </div>

        <div id="videoPreview" class="hidden mb-6">
            <h3 class="text-lg font-semibold mb-2">Video Preview</h3>
            <video id="previewPlayer" controls class="w-full rounded-lg" style="max-height: 400px;"></video>
        </div>

        <div class="flex gap-4">
            <button type="button" id="uploadBtn" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 disabled:opacity-50" disabled>
                Upload Video
            </button>
            <button type="button" id="cancelBtn" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400 hidden">
                Cancel
            </button>
            <a href="{{ url()->previous() }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                Back
            </a>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoFileInput = document.getElementById('videoFile');
    const dropZone = document.getElementById('dropZone');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const uploadBtn = document.getElementById('uploadBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const videoPreview = document.getElementById('videoPreview');
    const previewPlayer = document.getElementById('previewPlayer');

    const uploadUrl = '{{ $uploadUrl }}';
    let selectedFile = null;
    let xhr = null;

    // File input change
    videoFileInput.addEventListener('change', function(e) {
        handleFileSelect(e.target.files[0]);
    });

    // Drag and drop
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.classList.add('border-blue-500', 'bg-blue-50');
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        if (e.dataTransfer.files.length > 0) {
            handleFileSelect(e.dataTransfer.files[0]);
        }
    });

    function handleFileSelect(file) {
        if (!file.type.startsWith('video/')) {
            showError('Please select a valid video file.');
            return;
        }

        selectedFile = file;
        fileName.textContent = file.name;
        fileName.classList.remove('hidden');
        fileSize.textContent = formatFileSize(file.size);
        fileSize.classList.remove('hidden');
        uploadBtn.disabled = false;

        // Preview video
        const url = URL.createObjectURL(file);
        previewPlayer.src = url;
        videoPreview.classList.remove('hidden');

        hideMessages();
    }

    uploadBtn.addEventListener('click', function() {
        if (!selectedFile) return;

        const formData = new FormData();
        formData.append('video', selectedFile);
        formData.append('_token', '{{ csrf_token() }}');

        uploadBtn.disabled = true;
        cancelBtn.classList.remove('hidden');
        uploadProgress.classList.remove('hidden');
        progressBar.style.width = '0%';
        progressText.textContent = 'Uploading...';

        xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.style.width = percentComplete + '%';
                progressText.textContent = `Uploading... ${Math.round(percentComplete)}%`;
            }
        });

        xhr.addEventListener('load', function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                showSuccess('Video uploaded successfully!');
                progressBar.style.width = '100%';
                progressText.textContent = 'Upload complete!';

                // Redirect or update UI
                setTimeout(function() {
                    window.location.href = '{{ url()->previous() }}';
                }, 2000);
            } else {
                const response = JSON.parse(xhr.responseText);
                showError(response.message || 'Upload failed. Please try again.');
                uploadBtn.disabled = false;
            }
            cancelBtn.classList.add('hidden');
        });

        xhr.addEventListener('error', function() {
            showError('Upload failed. Please check your connection and try again.');
            uploadBtn.disabled = false;
            cancelBtn.classList.add('hidden');
        });

        xhr.open('POST', uploadUrl);
        xhr.send(formData);
    });

    cancelBtn.addEventListener('click', function() {
        if (xhr) {
            xhr.abort();
        }
        resetUpload();
    });

    function resetUpload() {
        selectedFile = null;
        videoFileInput.value = '';
        fileName.classList.add('hidden');
        fileSize.classList.add('hidden');
        uploadBtn.disabled = true;
        cancelBtn.classList.add('hidden');
        uploadProgress.classList.add('hidden');
        videoPreview.classList.add('hidden');
        progressBar.style.width = '0%';
    }

    function showError(message) {
        if (typeof window.showToast === 'function') {
            window.showToast(message, 'error');
        } else {
            alert(message);
        }
    }

    function showSuccess(message) {
        if (typeof window.showToast === 'function') {
            window.showToast(message, 'success');
        } else {
            alert(message);
        }
    }

    function hideMessages() {
        // Messages are now handled by global toast notifications
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
});
</script>
@endpush
@endsection

