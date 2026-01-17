<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'BluMark')); ?></title>

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
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 sidebar-gradient">
            <!-- Logo -->
            <div class="flex items-center h-16 px-6 border-b border-dark-700">
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-white">Blu<span class="text-primary-400">Mark</span></span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1">
                <a href="<?php echo e(route('dashboard')); ?>"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo e(request()->routeIs('dashboard') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white hover:bg-dark-700'); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                <a href="<?php echo e(route('jobs.index')); ?>"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo e(request()->routeIs('jobs.*') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white hover:bg-dark-700'); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    My Jobs
                </a>
                <a href="<?php echo e(route('billing.index')); ?>"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo e(request()->routeIs('billing.*') ? 'bg-primary-600 text-white' : 'text-gray-400 hover:text-white hover:bg-dark-700'); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Billing
                </a>
            </nav>

            <!-- User Menu -->
            <?php if(auth()->guard()->check()): ?>
            <div class="p-4 border-t border-dark-700">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-400 hover:text-white hover:bg-dark-700 rounded-lg transition-colors">
                        <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white font-semibold text-sm mr-3">
                            <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-white text-sm font-medium truncate"><?php echo e(Auth::user()->name); ?></p>
                            <p class="text-gray-500 text-xs truncate"><?php echo e(Auth::user()->email); ?></p>
                        </div>
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute bottom-full left-0 right-0 mb-2 bg-dark-700 rounded-lg shadow-lg border border-dark-600 overflow-hidden">
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
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
            <?php endif; ?>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 lg:pl-64">
            <!-- Top Navigation (Mobile) -->
            <header class="lg:hidden bg-dark-900 border-b border-dark-700 sticky top-0 z-40">
                <div class="flex items-center justify-between h-16 px-4">
                    <a href="/" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-white">Blu<span class="text-primary-400">Mark</span></span>
                    </a>

                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="p-2 text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <!-- Mobile Menu -->
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-48 bg-dark-800 rounded-lg shadow-lg border border-dark-700 overflow-hidden z-50">
                            <a href="<?php echo e(route('dashboard')); ?>" class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">Dashboard</a>
                            <a href="<?php echo e(route('jobs.index')); ?>" class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">My Jobs</a>
                            <a href="<?php echo e(route('billing.index')); ?>" class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">Billing</a>
                            <div class="border-t border-dark-700"></div>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="block w-full text-left px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-dark-700">Sign out</button>
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
                <?php echo e($slot); ?>

            </main>
        </div>
    </div>
</body>
</html>
<?php /**PATH /var/www/html/watermarking/resources/views/layouts/app.blade.php ENDPATH**/ ?>