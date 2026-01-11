<!-- Navigation -->
<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <!-- Logo -->
            <div class="flex items-center space-x-2">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('logo.jpeg') }}" alt="KITAB ASAN Logo" class="h-10 w-auto">
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('home') }}"
                   class="text-gray-700 hover:text-blue-600 font-medium transition {{ request()->routeIs('home') ? 'text-blue-600 font-semibold' : '' }}">
                    Home
                </a>
                <a href="{{ route('courses.index') }}"
                   class="text-gray-700 hover:text-blue-600 font-medium transition {{ request()->routeIs('courses.*') ? 'text-blue-600 font-semibold' : '' }}">
                    Courses
                </a>
                <a href="{{ route('about') }}"
                   class="text-gray-700 hover:text-blue-600 font-medium transition {{ request()->routeIs('about') ? 'text-blue-600 font-semibold' : '' }}">
                    About Us
                </a>
                <a href="{{ route('contact') }}"
                   class="text-gray-700 hover:text-blue-600 font-medium transition {{ request()->routeIs('contact') ? 'text-blue-600 font-semibold' : '' }}">
                    Contact
                </a>

                @auth
                    <a href="{{ route('student.dashboard') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="text-gray-700 hover:text-blue-600 font-medium transition {{ request()->routeIs('login') ? 'text-blue-600 font-semibold' : '' }}">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                        Sign Up
                    </a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-button"
                    type="button"
                    class="md:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                    aria-label="Toggle menu"
                    aria-expanded="false"
                    aria-controls="mobile-menu">
                <svg id="menu-icon" class="w-6 h-6 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <svg id="close-icon" class="w-6 h-6 hidden transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobile-menu"
             class="hidden md:hidden pb-4 border-t border-gray-200"
             role="menu"
             aria-labelledby="mobile-menu-button">
            <div class="flex flex-col space-y-1 pt-2">
                <a href="{{ route('home') }}" role="menuitem"
                   class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg font-medium transition {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Home
                    </span>
                </a>
                <a href="{{ route('courses.index') }}" role="menuitem"
                   class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg font-medium transition {{ request()->routeIs('courses.*') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Courses
                    </span>
                </a>
                <a href="{{ route('about') }}" role="menuitem"
                   class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg font-medium transition {{ request()->routeIs('about') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        About Us
                    </span>
                </a>
                <a href="{{ route('contact') }}" role="menuitem"
                   class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg font-medium transition {{ request()->routeIs('contact') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Contact
                    </span>
                </a>

                @auth
                    <a href="{{ route('student.dashboard') }}" role="menuitem"
                       class="block px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-center transition mt-3 shadow-md">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Dashboard
                        </span>
                    </a>
                @else
                    <a href="{{ route('login') }}" role="menuitem"
                       class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg font-medium transition {{ request()->routeIs('login') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Login
                        </span>
                    </a>
                    <a href="{{ route('register') }}" role="menuitem"
                       class="block px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-center transition mt-2 shadow-md">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Sign Up
                        </span>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

@push('styles')
<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.2s ease-out;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');
    const closeIcon = document.getElementById('close-icon');

    if (mobileMenuButton && mobileMenu && menuIcon && closeIcon) {
        // Toggle mobile menu
        mobileMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            const isHidden = mobileMenu.classList.contains('hidden');

            if (isHidden) {
                // Show menu
                mobileMenu.classList.remove('hidden');
                mobileMenu.classList.add('animate-fadeIn');
                menuIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
                mobileMenuButton.setAttribute('aria-expanded', 'true');
            } else {
                // Hide menu
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('animate-fadeIn');
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
                mobileMenuButton.setAttribute('aria-expanded', 'false');
            }
        });

        // Close menu when clicking outside (with a small delay to avoid conflicts)
        document.addEventListener('click', function(event) {
            if (!mobileMenu.classList.contains('hidden')) {
                const isClickInside = mobileMenu.contains(event.target) || mobileMenuButton.contains(event.target);

                if (!isClickInside) {
                    setTimeout(function() {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('animate-fadeIn');
                        menuIcon.classList.remove('hidden');
                        closeIcon.classList.add('hidden');
                        mobileMenuButton.setAttribute('aria-expanded', 'false');
                    }, 10);
                }
            }
        });

        // Close menu when clicking on a link (for better UX on mobile)
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                setTimeout(function() {
                    mobileMenu.classList.add('hidden');
                    mobileMenu.classList.remove('animate-fadeIn');
                    menuIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                    mobileMenuButton.setAttribute('aria-expanded', 'false');
                }, 100);
            });
        });

        // Close menu on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('animate-fadeIn');
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
                mobileMenuButton.setAttribute('aria-expanded', 'false');
            }
        });
    }
});
</script>
@endpush
