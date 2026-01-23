<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BluMark') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        dark: {
                            900: '#0f1419',
                            800: '#1a1f26',
                            700: '#2d333b',
                            600: '#3d444d',
                        }
                    },
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-gradient {
            background: linear-gradient(180deg, #0f1419 0%, #1a1f26 100%);
        }
    </style>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Impersonation Banner -->
    @if(session('impersonating'))
    <div class="bg-yellow-500 text-yellow-900 text-center py-2 px-4 text-sm font-medium sticky top-0 z-50">
        <span>You are impersonating <strong>{{ Auth::user()->name }}</strong>.</span>
        <form action="{{ route('admin.impersonate.stop') }}" method="POST" class="inline ml-4">
            @csrf
            <button type="submit" class="underline hover:no-underline font-semibold">
                Stop Impersonating
            </button>
        </form>
    </div>
    @endif

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 sidebar-gradient">
            <!-- Logo -->
            <div class="flex items-center h-16 px-6 border-b border-dark-700">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-bold text-white">Blu<span class="text-primary-400">Mark</span></span>
                        <span class="text-[9px] text-gray-500 -mt-0.5">Security Built Into Every File</span>
                    </div>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white hover:bg-dark-700' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <!-- Watermarking Section -->
                <div class="pt-4 mt-4 border-t border-dark-700">
                    <p class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Watermarking</p>
                    <a href="{{ route('jobs.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('jobs.*') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white hover:bg-dark-700' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        My Jobs
                    </a>
                    <a href="{{ route('batch.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('batch.*') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white hover:bg-dark-700' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Batch Jobs
                    </a>
                    <a href="{{ route('templates.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('templates.*') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white hover:bg-dark-700' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                        </svg>
                        Templates
                    </a>
                </div>

                <!-- Submissions Section -->
                <div class="pt-4 mt-4 border-t border-dark-700">
                    <p class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Submissions</p>
                    <a href="{{ route('lenders.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('lenders.*') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white hover:bg-dark-700' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Manage Lenders
                    </a>
                    <a href="{{ route('distributions.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('distributions.*') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white hover:bg-dark-700' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Submissions
                    </a>
                    <a href="{{ route('email-templates.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('email-templates.*') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white hover:bg-dark-700' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Email Templates
                    </a>
                    <a href="{{ route('smtp-settings.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('smtp-settings.*') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white hover:bg-dark-700' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        SMTP Settings
                    </a>
                </div>

                <!-- Sharing Section -->
                <div class="pt-4 mt-4 border-t border-dark-700">
                    <p class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Sharing</p>
                    <a href="{{ route('shares.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('shares.*') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white hover:bg-dark-700' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                        Shared Links
                    </a>
                </div>

                <!-- Security Section -->
                <div class="pt-4 mt-4 border-t border-dark-700">
                    <p class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Security</p>
                    <a href="{{ route('verify.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('verify.*') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white hover:bg-dark-700' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Verify Document
                    </a>
                    @if(Auth::check() && Auth::user()->isAdmin())
                    <a href="{{ route('admin.audit.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.audit.*') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white hover:bg-dark-700' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        Security Audit
                    </a>
                    <a href="{{ route('admin.ocr.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ocr.*') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white hover:bg-dark-700' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        OCR Testing
                    </a>
                    @endif
                </div>

                <!-- Account Section -->
                <div class="pt-4 mt-4 border-t border-dark-700">
                    <p class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Account</p>
                    <a href="{{ route('billing.index') }}"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('billing.*') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white hover:bg-dark-700' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Billing
                    </a>
                </div>

                @if(Auth::check() && Auth::user()->isAdmin())
                <!-- Admin Section -->
                <div class="pt-4 mt-4 border-t border-dark-700">
                    <p class="px-3 mb-2 text-xs font-semibold text-purple-400 uppercase tracking-wider">Admin</p>
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-purple-600 text-white' : 'text-purple-400 hover:text-white hover:bg-purple-900' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Admin Panel
                    </a>
                </div>
                @endif
            </nav>

            <!-- User Menu -->
            @auth
            <div class="p-4 border-t border-dark-700">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-400 hover:text-white hover:bg-dark-700 rounded-lg transition-colors">
                        <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white font-semibold text-sm mr-3">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-white text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                            <p class="text-gray-500 text-xs truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute bottom-full left-0 right-0 mb-2 bg-dark-700 rounded-lg shadow-lg border border-dark-600 overflow-hidden">
                        <a href="{{ route('profile.show') }}" class="flex items-center w-full px-4 py-3 text-sm text-gray-400 hover:text-white hover:bg-dark-600 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            My Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-3 text-sm text-gray-400 hover:text-white hover:bg-dark-600 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endauth
        </aside>

        <!-- Main Content -->
        <div class="flex-1 lg:pl-64">
            <!-- Top Navigation (Mobile) -->
            <header class="lg:hidden bg-dark-900 border-b border-dark-700 sticky top-0 z-40">
                <div class="flex items-center justify-between h-16 px-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-lg font-bold text-white">Blu<span class="text-primary-400">Mark</span></span>
                            <span class="text-[9px] text-gray-500 -mt-0.5">Security Built Into Every File</span>
                        </div>
                    </a>

                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="p-2 text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <!-- Mobile Menu -->
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-56 bg-dark-800 rounded-lg shadow-lg border border-dark-700 overflow-hidden z-50 max-h-96 overflow-y-auto">
                            <a href="{{ route('dashboard') }}" class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">Dashboard</a>
                            <div class="border-t border-dark-700"></div>
                            <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Watermarking</p>
                            <a href="{{ route('jobs.index') }}" class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">My Jobs</a>
                            <a href="{{ route('batch.index') }}" class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">Batch Jobs</a>
                            <a href="{{ route('templates.index') }}" class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">Templates</a>
                            <div class="border-t border-dark-700"></div>
                            <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Submissions</p>
                            <a href="{{ route('lenders.index') }}" class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">Manage Lenders</a>
                            <a href="{{ route('distributions.index') }}" class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">Submissions</a>
                            <a href="{{ route('email-templates.index') }}" class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">Email Templates</a>
                            <a href="{{ route('smtp-settings.index') }}" class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">SMTP Settings</a>
                            <div class="border-t border-dark-700"></div>
                            <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Sharing</p>
                            <a href="{{ route('shares.index') }}" class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">Shared Links</a>
                            <div class="border-t border-dark-700"></div>
                            <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Security</p>
                            <a href="{{ route('verify.index') }}" class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">Verify Document</a>
                            @if(Auth::check() && Auth::user()->isAdmin())
                            <a href="{{ route('admin.audit.index') }}" class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">Security Audit</a>
                            <a href="{{ route('admin.ocr.index') }}" class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">OCR Testing</a>
                            @endif
                            <div class="border-t border-dark-700"></div>
                            <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Account</p>
                            <a href="{{ route('billing.index') }}" class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">Billing</a>
                            <a href="{{ route('profile.show') }}" class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">My Profile</a>
                            @if(Auth::check() && Auth::user()->isAdmin())
                            <div class="border-t border-dark-700"></div>
                            <p class="px-4 py-2 text-xs font-semibold text-purple-400 uppercase">Admin</p>
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 text-sm text-purple-300 hover:text-white hover:bg-purple-900">Admin Panel</a>
                            @endif
                            <div class="border-t border-dark-700"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">Sign out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Header -->
            @isset($header)
            <header class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endisset

            <!-- Flash Messages -->
            @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-xl" role="alert">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm text-green-700">{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                <div class="flex items-center p-4 bg-red-50 border border-red-200 rounded-xl" role="alert">
                    <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm text-red-700">{{ session('error') }}</span>
                </div>
            </div>
            @endif

            <!-- Page Content -->
            <main class="py-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
