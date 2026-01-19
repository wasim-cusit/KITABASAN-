<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student - KITAB ASAN')</title>

    <!-- Vite Assets (Tailwind CSS, Alpine.js, AOS, etc.) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Styles -->
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        @media (max-width: 768px) {
            .sidebar-overlay {
                display: none;
            }
            .sidebar-overlay.active {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 40;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-100" x-data="{ sidebarOpen: false, profileMenuOpen: false }">
    <div class="min-h-screen flex">
        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="sidebar-overlay lg:hidden" x-cloak></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-gray-800 text-white min-h-screen transform transition-transform duration-300 ease-in-out lg:translate-x-0">
            <div class="p-4">
                <div class="mb-8">
                    <!-- Close button (Mobile only) -->
                    <div class="lg:hidden flex justify-end mb-4">
                        <button @click="sidebarOpen = false" class="text-white hover:text-gray-300 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Logo and Name with Role (All Screens) -->
                    <div class="pb-4 border-b border-gray-700">
                        <!-- Logo -->
                        <a href="{{ route('student.dashboard') }}" class="flex justify-center mb-3 hover:opacity-80 transition">
                            <img src="{{ asset('logo.jpeg') }}" alt="Logo" class="h-12">
                        </a>
                        <!-- Name and Role -->
                        <div class="text-center">
                            <p class="text-white font-semibold text-sm">
                                {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                            </p>
                            <p class="text-gray-400 text-xs mt-1">Student</p>
                        </div>
                    </div>
                </div>

                <nav class="space-y-2">
                    <a href="{{ route('student.dashboard') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('student.dashboard') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('student.courses.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('student.courses.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span>Browse Courses</span>
                    </a>

                    <a href="{{ route('student.payments.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('student.payments.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span>Payments</span>
                    </a>

                    <a href="{{ route('student.chatbot.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('student.chatbot.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <span>Chatbot</span>
                    </a>

                    <a href="{{ route('student.devices.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('student.devices.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                        <span>Devices</span>
                    </a>

                    <a href="{{ route('student.profile.show') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('student.profile.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span>Profile</span>
                    </a>

                    <a href="{{ route('student.settings.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('student.settings.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>Settings</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-0">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-4 lg:px-6 py-4">
                    <div class="flex items-center space-x-4">
                        <!-- Mobile Menu Button -->
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600 hover:text-gray-900 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <h1 class="text-xl lg:text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <!-- Profile Dropdown (All Screen Sizes) -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg p-1">
                            @if(Auth::user()->profile_image)
                                <img src="{{ Auth::user()->getProfileImageUrl() }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-gray-300">
                            @else
                                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center border-2 border-gray-300">
                                    <span class="text-white font-semibold text-sm">{{ Auth::user()->getInitials() }}</span>
                                </div>
                            @endif
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl py-1 z-50 border border-gray-200 overflow-hidden"
                             x-cloak>
                            <!-- User Info Section -->
                            <div class="px-4 py-4 bg-gradient-to-br from-gray-50 to-gray-100 border-b border-gray-200">
                                <div class="mb-3">
                                    <p class="text-base font-semibold text-gray-900 mb-1">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                </div>
                                @if(Auth::user()->last_login_at)
                                    <div class="mt-3 pt-3 border-t border-gray-200/60">
                                        <div class="flex items-start space-x-2">
                                            <svg class="w-4 h-4 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Last Login</p>
                                                <p class="text-sm text-gray-900 font-semibold">{{ Auth::user()->last_login_at->format('M d, Y') }}</p>
                                                <p class="text-xs text-gray-500">{{ Auth::user()->last_login_at->format('h:i A') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-3 pt-3 border-t border-gray-200/60">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">First Login</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <!-- Menu Items -->
                            <div class="flex border-t border-gray-100 py-1">
                                <a href="{{ route('student.profile.edit') }}" class="flex-1 flex items-center justify-center space-x-1.5 px-4 py-3 mx-1 my-1 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150 border-r border-gray-200 rounded-lg">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="font-medium text-xs">Edit Profile</span>
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="flex items-center justify-center space-x-1.5 w-full px-4 py-3 mx-1 my-1 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150 rounded-lg">
                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        <span class="font-medium text-xs">Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-4 lg:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')

    <!-- Toast Notifications -->
    @include('components.notification-toast')
</body>
</html>

