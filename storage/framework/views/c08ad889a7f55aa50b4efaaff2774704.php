<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>Admin - <?php echo e(config('app.name', 'BluMark')); ?></title>

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
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
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
            background: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%);
        }
    </style>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Impersonation Banner -->
    <?php if(session('impersonating')): ?>
    <div class="bg-yellow-500 text-yellow-900 text-center py-2 px-4 text-sm font-medium">
        <span>You are impersonating <strong><?php echo e(Auth::user()->name); ?></strong>.</span>
        <form action="<?php echo e(route('admin.impersonate.stop')); ?>" method="POST" class="inline ml-4">
            <?php echo csrf_field(); ?>
            <button type="submit" class="underline hover:no-underline font-semibold">
                Stop Impersonating
            </button>
        </form>
    </div>
    <?php endif; ?>

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 sidebar-gradient <?php if(session('impersonating')): ?> lg:top-10 <?php endif; ?>">
            <!-- Logo -->
            <div class="flex items-center h-16 px-6 border-b border-indigo-800">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center space-x-2">
                    <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-bold text-white">Blu<span class="text-primary-300">Mark</span></span>
                        <span class="text-[9px] text-indigo-300 -mt-0.5">Security Built Into Every File</span>
                    </div>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1">
                <a href="<?php echo e(route('admin.dashboard')); ?>"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo e(request()->routeIs('admin.dashboard') ? 'bg-primary-600 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-800'); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                <a href="<?php echo e(route('admin.users.index')); ?>"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo e(request()->routeIs('admin.users.*') ? 'bg-primary-600 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-800'); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Users
                </a>
                <a href="<?php echo e(route('admin.jobs.index')); ?>"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo e(request()->routeIs('admin.jobs.*') ? 'bg-primary-600 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-800'); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Jobs
                </a>
                <a href="<?php echo e(route('admin.activity')); ?>"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo e(request()->routeIs('admin.activity') ? 'bg-primary-600 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-800'); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Activity Log
                </a>

                <div class="pt-4 mt-4 border-t border-indigo-800">
                    <p class="px-3 mb-2 text-xs font-semibold text-indigo-400 uppercase tracking-wider">Security</p>
                    <?php $newAlertCount = \App\Models\Alert::where('status', 'new')->count(); ?>
                    <a href="<?php echo e(route('admin.alerts.index')); ?>"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo e(request()->routeIs('admin.alerts.*') ? 'bg-primary-600 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-800'); ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Security Alerts
                        <?php if($newAlertCount > 0): ?>
                            <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full"><?php echo e($newAlertCount); ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?php echo e(route('admin.audit.index')); ?>"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo e(request()->routeIs('admin.audit.*') ? 'bg-primary-600 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-800'); ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Security Audit
                    </a>
                    <a href="<?php echo e(route('admin.ocr.index')); ?>"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo e(request()->routeIs('admin.ocr.*') ? 'bg-primary-600 text-white' : 'text-indigo-200 hover:text-white hover:bg-indigo-800'); ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        OCR Testing
                    </a>
                </div>

                <div class="pt-4 mt-4 border-t border-indigo-800">
                    <a href="<?php echo e(route('dashboard')); ?>"
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors text-indigo-200 hover:text-white hover:bg-indigo-800">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"/>
                        </svg>
                        Back to App
                    </a>
                </div>
            </nav>

            <!-- Admin User Menu -->
            <?php if(auth()->guard()->check()): ?>
            <div class="p-4 border-t border-indigo-800">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-indigo-200 hover:text-white hover:bg-indigo-800 rounded-lg transition-colors">
                        <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white font-semibold text-sm mr-3">
                            <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-white text-sm font-medium truncate"><?php echo e(Auth::user()->name); ?></p>
                            <p class="text-indigo-400 text-xs truncate"><?php echo e(Auth::user()->getRoleLabel()); ?></p>
                        </div>
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute bottom-full left-0 right-0 mb-2 bg-indigo-900 rounded-lg shadow-lg border border-indigo-700 overflow-hidden">
                        <a href="<?php echo e(route('profile.show')); ?>" class="flex items-center w-full px-4 py-3 text-sm text-indigo-200 hover:text-white hover:bg-indigo-800 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            My Profile
                        </a>
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="flex items-center w-full px-4 py-3 text-sm text-indigo-200 hover:text-white hover:bg-indigo-800 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 lg:pl-64">
            <!-- Top Navigation (Mobile) -->
            <header class="lg:hidden bg-indigo-900 border-b border-indigo-800 sticky top-0 z-40">
                <div class="flex items-center justify-between h-16 px-4">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-white">Admin<span class="text-primary-300">Panel</span></span>
                    </a>

                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="p-2 text-indigo-200 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <!-- Mobile Menu -->
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-48 bg-indigo-900 rounded-lg shadow-lg border border-indigo-700 overflow-hidden z-50">
                            <a href="<?php echo e(route('admin.dashboard')); ?>" class="block px-4 py-3 text-sm text-indigo-200 hover:text-white hover:bg-indigo-800">Dashboard</a>
                            <a href="<?php echo e(route('admin.users.index')); ?>" class="block px-4 py-3 text-sm text-indigo-200 hover:text-white hover:bg-indigo-800">Users</a>
                            <a href="<?php echo e(route('admin.jobs.index')); ?>" class="block px-4 py-3 text-sm text-indigo-200 hover:text-white hover:bg-indigo-800">Jobs</a>
                            <a href="<?php echo e(route('admin.activity')); ?>" class="block px-4 py-3 text-sm text-indigo-200 hover:text-white hover:bg-indigo-800">Activity Log</a>
                            <div class="border-t border-indigo-700"></div>
                            <p class="px-4 py-2 text-xs font-semibold text-indigo-400 uppercase">Security</p>
                            <a href="<?php echo e(route('admin.audit.index')); ?>" class="block px-4 py-3 text-sm text-indigo-200 hover:text-white hover:bg-indigo-800">Security Audit</a>
                            <a href="<?php echo e(route('admin.ocr.index')); ?>" class="block px-4 py-3 text-sm text-indigo-200 hover:text-white hover:bg-indigo-800">OCR Testing</a>
                            <div class="border-t border-indigo-700"></div>
                            <a href="<?php echo e(route('dashboard')); ?>" class="block px-4 py-3 text-sm text-indigo-200 hover:text-white hover:bg-indigo-800">Back to App</a>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="block w-full text-left px-4 py-3 text-sm text-indigo-200 hover:text-white hover:bg-indigo-800">Sign out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Header -->
            <?php if(isset($header)): ?>
            <header class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <?php echo e($header); ?>

                </div>
            </header>
            <?php endif; ?>

            <!-- Flash Messages -->
            <?php if(session('success')): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-xl" role="alert">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm text-green-700"><?php echo e(session('success')); ?></span>
                </div>
            </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                <div class="flex items-center p-4 bg-red-50 border border-red-200 rounded-xl" role="alert">
                    <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm text-red-700"><?php echo e(session('error')); ?></span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Page Content -->
            <main class="py-8">
                <?php if(isset($slot)): ?>
                    <?php echo e($slot); ?>

                <?php else: ?>
                    <?php echo $__env->yieldContent('content'); ?>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /var/www/html/watermarking/resources/views/layouts/admin.blade.php ENDPATH**/ ?>