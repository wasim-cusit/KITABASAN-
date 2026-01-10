<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $seo = isset($seo) ? $seo : \App\Services\SEOService::generateMetaTags([
            'title' => $__env->yieldContent('title', 'Kitabasan Learning Platform'),
            'description' => $__env->yieldContent('description', 'Kitabasan Learning Platform - Your trusted learning platform for quality education.'),
            'keywords' => $__env->yieldContent('keywords', 'online learning, courses, education, e-learning, kitabasan, online courses, study online'),
            'image' => $__env->yieldContent('og_image', asset('logo.jpeg')),
            'url' => url()->current(),
        ]);
    @endphp

    <!-- Primary Meta Tags -->
    <title>{{ $seo['title'] }}</title>
    <meta name="title" content="{{ $seo['title'] }}">
    <meta name="description" content="{{ $seo['description'] }}">
    <meta name="keywords" content="{{ $seo['keywords'] }}">
    <meta name="author" content="MUHAMMAD WASIM">
    <meta name="robots" content="index, follow">
    <meta name="language" content="{{ app()->getLocale() }}">
    <meta name="revisit-after" content="7 days">
    <meta name="theme-color" content="#2563eb">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ $seo['url'] }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $seo['type'] }}">
    <meta property="og:url" content="{{ $seo['url'] }}">
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:image" content="{{ $seo['image'] }}">
    <meta property="og:site_name" content="{{ $seo['site_name'] }}">
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $seo['url'] }}">
    <meta name="twitter:title" content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    <meta name="twitter:image" content="{{ $seo['image'] }}">

    <!-- Performance Optimizations -->
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://wa.me">
    <link rel="dns-prefetch" href="https://wa.me">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="PK">
    <meta name="geo.placename" content="Pakistan">
    <meta name="author" content="MUHAMMAD WASIM">
    <meta name="contact" content="+923342372772">
    <meta name="copyright" content="Kitabasan Learning Platform">
    <meta name="distribution" content="global">
    <meta name="rating" content="general">

    <!-- Hidden Keywords for SEO -->
    <meta name="keywords" content="{{ $seo['keywords'] }}, online education pakistan, e-learning platform pakistan, study online pakistan, courses pakistan, learn online pakistan, educational platform, skill development, professional training, online classes, virtual learning, distance education, online training, course platform, learning management system, lms, online school, digital education, educational technology, edtech">

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpeg') }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Styles -->
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        /* Lazy loading for images */
        img[loading="lazy"] { opacity: 0; transition: opacity 0.3s; }
        img[loading="lazy"].loaded { opacity: 1; }
    </style>

    @stack('styles')

    <!-- Structured Data -->
    @stack('structured_data')
</head>
<body>
    <div class="min-h-screen bg-gray-50">
        @include('partials.navigation')

        @yield('content')
    </div>

    <!-- Scripts -->
    <script>
        // CSRF Token setup for AJAX requests
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Lazy loading images performance optimization
        if ('loading' in HTMLImageElement.prototype) {
            const images = document.querySelectorAll('img[loading="lazy"]');
            images.forEach(img => {
                img.addEventListener('load', function() {
                    this.classList.add('loaded');
                });
            });
        } else {
            // Fallback for browsers that don't support native lazy loading
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js';
            document.body.appendChild(script);
            script.onload = function() {
                const observer = lozad('.lozad', {
                    loaded: function(el) {
                        el.classList.add('loaded');
                    }
                });
                observer.observe();
            };
        }

        // Preload critical resources
        if (document.querySelector('link[rel="preload"]') === null) {
            const preloadLink = document.createElement('link');
            preloadLink.rel = 'preload';
            preloadLink.as = 'image';
            preloadLink.href = '{{ asset("logo.jpeg") }}';
            document.head.appendChild(preloadLink);
        }
    </script>

    @stack('scripts')
</body>
</html>
