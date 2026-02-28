@props([
    'course',
    'useThumbnail' => false,
    'class' => '',
    'imgClass' => 'w-full h-full object-cover',
])

@php
    $url = $useThumbnail ? $course->getThumbnailUrl() : $course->getCoverImageUrl();
    $hasImage = $useThumbnail ? $course->hasValidThumbnail() : $course->hasValidCoverImage();
    $initial = $course->getTitleInitial();
@endphp

@if($hasImage && $url)
    <img
        src="{{ $url }}"
        alt="{{ $course->title }}"
        class="{{ $imgClass }} {{ $class }}"
        loading="lazy"
        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
    >
    <div class="hidden w-full h-full min-h-[120px] flex items-center justify-center bg-gradient-to-br from-blue-400 to-indigo-600 text-white text-3xl font-bold course-image-placeholder">
        {{ $initial }}
    </div>
@else
    <div class="w-full h-full min-h-[120px] flex items-center justify-center bg-gradient-to-br from-blue-400 to-indigo-600 text-white text-3xl font-bold {{ $class }}">
        {{ $initial }}
    </div>
@endif
