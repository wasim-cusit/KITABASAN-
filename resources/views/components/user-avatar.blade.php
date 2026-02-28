@props([
    'user',
    'size' => 'md', // sm, md, lg, xl
    'class' => '',
])

@php
    $sizeClasses = [
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-12 h-12 text-sm',
        'xl' => 'w-24 h-24 text-2xl',
        'custom' => '',
    ][$size] ?? 'w-10 h-10 text-sm';
    $hasImage = $user->hasEffectiveProfileImage();
    $imageUrl = $hasImage ? $user->getEffectiveProfileImageUrl() : null;
@endphp

<div
    class="flex-shrink-0 rounded-full overflow-hidden flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 border-2 border-gray-200 {{ $sizeClasses }} {{ $class }}"
    data-user-avatar
>
    @if($imageUrl)
        <img
            src="{{ $imageUrl }}"
            alt="{{ $user->name }}"
            class="w-full h-full object-cover avatar-img"
            loading="lazy"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
        >
        <span class="hidden w-full h-full flex items-center justify-center text-white font-semibold avatar-initials">{{ $user->getInitials() }}</span>
    @else
        <span class="text-white font-semibold">{{ $user->getInitials() }}</span>
    @endif
</div>
