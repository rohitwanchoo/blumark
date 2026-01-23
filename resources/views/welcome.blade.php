<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Secure PDF watermarking platform for financial documents. Add ISO and Lender watermarks to protect your sensitive documents.">
    <title>{{ config('app.name', 'PDF Watermark Platform') }} - Secure Document Protection</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

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
                            950: '#082f49',
                        },
                        dark: {
                            900: '#0f1419',
                            800: '#1a1f26',
                            700: '#2d333b',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.8s ease-out forwards',
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                        'fade-in-delay': 'fadeIn 0.8s ease-out 0.2s forwards',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-text {
            background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .hero-gradient {
            background: linear-gradient(135deg, #0f1419 0%, #1a1f26 50%, #0c4a6e 100%);
        }
        .feature-gradient {
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-900">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-20">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-bold text-white">Blu<span class="text-primary-400">Mark</span></span>
                        <span class="text-[10px] text-gray-400 -mt-1">Security Built Into Every File</span>
                    </div>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Features</a>
                    <a href="#how-it-works" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">How It Works</a>
                    <a href="#use-cases" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Use Cases</a>
                    <a href="#security" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Security</a>
                    <a href="/docs/api" class="text-gray-300 hover:text-white transition-colors text-sm font-medium">Developers</a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow-lg shadow-primary-600/25">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white text-sm font-medium transition-colors hidden sm:block">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow-lg shadow-primary-600/25">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient min-h-screen flex items-center relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <!-- Badge -->
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-primary-900/50 border border-primary-700/50 mb-8 animate-fade-in">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                    <span class="text-primary-300 text-sm font-medium">Built for MCA Industry</span>
                </div>

                <!-- Headline -->
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold text-white mb-6 animate-fade-in-up" style="animation-delay: 0.1s; opacity: 0;">
                    Build Trust.
                    <span class="block gradient-text">Close More Deals.</span>
                </h1>

                <!-- Subheadline -->
                <p class="text-lg sm:text-xl text-gray-400 mb-10 max-w-2xl mx-auto animate-fade-in-up" style="animation-delay: 0.2s; opacity: 0;">
                    Dual watermarks: ISO name + Lender name on every document.
                    ISOs prove deal ownership. Lenders get clean submissions. Everyone wins.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up" style="animation-delay: 0.3s; opacity: 0;">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-2xl shadow-primary-600/30 hover:shadow-primary-600/40 hover:-translate-y-0.5">
                            <span>Go to Dashboard</span>
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-2xl shadow-primary-600/30 hover:shadow-primary-600/40 hover:-translate-y-0.5">
                            <span>Start Free Today</span>
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl transition-all duration-200 border border-white/20">
                            <span>Sign In</span>
                        </a>
                    @endauth
                </div>

                <!-- Trust Indicators -->
                <div class="mt-16 animate-fade-in-up" style="animation-delay: 0.4s; opacity: 0;">
                    <p class="text-gray-500 text-sm mb-4">Protect high-risk MCA documents</p>
                    <div class="flex flex-wrap items-center justify-center gap-4 md:gap-8 text-gray-600">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-400 text-sm">Bank Statements</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-400 text-sm">Applications</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-400 text-sm">Term Sheets</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-400 text-sm">Voided Checks</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-400 text-sm">IDs & Licenses</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-4 py-1.5 bg-primary-100 text-primary-700 text-sm font-semibold rounded-full mb-4">Features</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    Professional Document Standards for MCA
                </h2>
                <p class="text-lg text-gray-600">
                    When ISOs and lenders both use watermarking, deals move faster, disputes disappear, and everyone operates with confidence.
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group p-8 rounded-2xl border border-gray-200 hover:border-primary-200 hover:shadow-xl transition-all duration-300 bg-white">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Dual Watermark System</h3>
                    <p class="text-gray-600">
                        Two watermarks on every page: <strong>Your ISO</strong> (who's sending) + <strong>Lender name</strong> (who's receiving). Send same docs to multiple lenders — each uniquely marked.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="group p-8 rounded-2xl border border-gray-200 hover:border-primary-200 hover:shadow-xl transition-all duration-300 bg-white">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4l16 16M4 20L20 4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Diagonal Watermark</h3>
                    <p class="text-gray-600">
                        One bold diagonal watermark spans the entire page. Impossible to crop, screenshot, or edit around. Full page protection.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="group p-8 rounded-2xl border border-gray-200 hover:border-primary-200 hover:shadow-xl transition-all duration-300 bg-white">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Stop Statement Recycling</h3>
                    <p class="text-gray-600">
                        Watermarked docs are obviously contaminated when recycled. "Prepared for XYZ Funding" tells lenders to reject.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="group p-8 rounded-2xl border border-gray-200 hover:border-primary-200 hover:shadow-xl transition-all duration-300 bg-white">
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Instant Processing</h3>
                    <p class="text-gray-600">
                        Watermark and download in seconds. No delay on deals. Works with large bank statement packages.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="group p-8 rounded-2xl border border-gray-200 hover:border-primary-200 hover:shadow-xl transition-all duration-300 bg-white">
                    <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Legal Protection</h3>
                    <p class="text-gray-600">
                        If a merchant claims "shared without consent," watermarks prove who accessed what, when, and under which application.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="group p-8 rounded-2xl border border-gray-200 hover:border-primary-200 hover:shadow-xl transition-all duration-300 bg-white">
                    <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Psychological Deterrence</h3>
                    <p class="text-gray-600">
                        Even dishonest actors behave when their name is permanently burned into every page. Stops theft before it happens.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-4 py-1.5 bg-primary-100 text-primary-700 text-sm font-semibold rounded-full mb-4">How It Works</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    Dual Watermark System
                </h2>
                <p class="text-lg text-gray-600">
                    Every document gets two watermarks: <strong>Your ISO</strong> (sender) and <strong>Lender name</strong> (recipient). Send the same application to multiple lenders — each copy uniquely traceable.
                </p>
            </div>

            <!-- Steps -->
            <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
                <!-- Step 1 -->
                <div class="relative text-center">
                    <div class="w-16 h-16 bg-primary-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6 shadow-lg shadow-primary-600/30">
                        1
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Upload Application</h3>
                    <p class="text-gray-600">
                        Upload your merchant's bank statements, application, or term sheet that you want to send to lenders.
                    </p>
                    <!-- Connector Line -->
                    <div class="hidden md:block absolute top-8 left-[60%] w-[80%] h-0.5 bg-gradient-to-r from-primary-300 to-transparent"></div>
                </div>

                <!-- Step 2 -->
                <div class="relative text-center">
                    <div class="w-16 h-16 bg-primary-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6 shadow-lg shadow-primary-600/30">
                        2
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Add ISO + Lender</h3>
                    <p class="text-gray-600">
                        Enter <strong>your ISO name</strong> (sender) and the <strong>Lender name</strong> you're sending to. Both appear on every page.
                    </p>
                    <!-- Connector Line -->
                    <div class="hidden md:block absolute top-8 left-[60%] w-[80%] h-0.5 bg-gradient-to-r from-primary-300 to-transparent"></div>
                </div>

                <!-- Step 3 -->
                <div class="relative text-center">
                    <div class="w-16 h-16 bg-primary-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6 shadow-lg shadow-primary-600/30">
                        3
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Send to Multiple Lenders</h3>
                    <p class="text-gray-600">
                        Create a separate watermarked copy for each lender. Same ISO, different lender names = full traceability.
                    </p>
                </div>
            </div>

            <!-- Visual Example of Multi-Lender Workflow -->
            <div class="mt-16 bg-white rounded-3xl p-8 lg:p-12 border border-gray-200 shadow-sm">
                <h4 class="text-xl font-bold text-gray-900 mb-8 text-center">Example: Same Application, Multiple Lenders</h4>
                <div class="grid md:grid-cols-3 gap-6">
                    <!-- Copy 1 -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-semibold text-blue-700">Copy for Lender A</span>
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-blue-100">
                            <div class="space-y-2">
                                <div class="text-xs font-mono text-blue-600 bg-blue-50 px-2 py-1 rounded">ISO: ABC Partners</div>
                                <div class="text-xs font-mono text-blue-600 bg-blue-50 px-2 py-1 rounded">Lender: FastFund Capital</div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-3 text-center">Sent to FastFund Capital</p>
                    </div>

                    <!-- Copy 2 -->
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-200">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-semibold text-emerald-700">Copy for Lender B</span>
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-emerald-100">
                            <div class="space-y-2">
                                <div class="text-xs font-mono text-emerald-600 bg-emerald-50 px-2 py-1 rounded">ISO: ABC Partners</div>
                                <div class="text-xs font-mono text-emerald-600 bg-emerald-50 px-2 py-1 rounded">Lender: QuickCash MCA</div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-3 text-center">Sent to QuickCash MCA</p>
                    </div>

                    <!-- Copy 3 -->
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-200">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-semibold text-purple-700">Copy for Lender C</span>
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-purple-100">
                            <div class="space-y-2">
                                <div class="text-xs font-mono text-purple-600 bg-purple-50 px-2 py-1 rounded">ISO: ABC Partners</div>
                                <div class="text-xs font-mono text-purple-600 bg-purple-50 px-2 py-1 rounded">Lender: BlueWave Funding</div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-3 text-center">Sent to BlueWave Funding</p>
                    </div>
                </div>

                <div class="mt-8 bg-gray-100 rounded-xl p-4 text-center">
                    <p class="text-gray-700 text-sm">
                        <strong>Result:</strong> Same ISO (ABC Partners) on all copies, but each lender gets a copy with <em>their specific name</em>.
                        If any copy leaks, you know exactly which lender received it.
                    </p>
                </div>
            </div>

            <!-- CTA -->
            <div class="text-center mt-16">
                @guest
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 bg-dark-900 hover:bg-dark-800 text-white font-semibold rounded-xl transition-all duration-200 shadow-xl hover:-translate-y-0.5">
                    <span>Get Started Now</span>
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                @else
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 bg-dark-900 hover:bg-dark-800 text-white font-semibold rounded-xl transition-all duration-200 shadow-xl hover:-translate-y-0.5">
                    <span>Go to Dashboard</span>
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                @endguest
            </div>
        </div>
    </section>

    <!-- Use Cases Section - MCA Industry -->
    <section id="use-cases" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-4 py-1.5 bg-primary-100 text-primary-700 text-sm font-semibold rounded-full mb-4">Win-Win for Everyone</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    Better for ISOs. Better for Lenders.
                </h2>
                <p class="text-lg text-gray-600">
                    Watermarking creates transparency that benefits the entire MCA ecosystem. ISOs get credit for their deals. Lenders get organized submissions. Merchants get faster approvals.
                </p>
            </div>

            <!-- Four Key Benefits Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
                <!-- Clear Attribution -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Clear Attribution</h3>
                    <p class="text-gray-600 text-sm">ISO + Lender on every page. ISOs get proper credit. Lenders know exactly who sent each deal. No confusion.</p>
                </div>

                <!-- Faster Deals -->
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-100">
                    <div class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Faster Deals</h3>
                    <p class="text-gray-600 text-sm">Clean, organized submissions move through underwriting faster. Less back-and-forth. Quicker funding for everyone.</p>
                </div>

                <!-- Professional Standards -->
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-100">
                    <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Industry Standard</h3>
                    <p class="text-gray-600 text-sm">Leading ISOs and lenders already watermark. It's becoming the mark of a professional, trustworthy operation.</p>
                </div>

                <!-- Mutual Protection -->
                <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-6 border border-orange-100">
                    <div class="w-12 h-12 bg-orange-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Mutual Protection</h3>
                    <p class="text-gray-600 text-sm">Both parties protected if questions arise. Clear documentation benefits ISOs and lenders equally.</p>
                </div>
            </div>

            <!-- Two Column Use Cases -->
            <div class="grid lg:grid-cols-2 gap-8">
                <!-- Lender Use Case -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-3xl p-8 lg:p-10 border border-blue-100">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg shadow-blue-500/30">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">For Lenders</h3>
                            <p class="text-blue-600 font-medium">Lenders, Syndicators & Underwriters</p>
                        </div>
                    </div>

                    <p class="text-gray-600 mb-6">
                        Receive clean, organized submissions from your ISO partners. Know exactly who sent each deal, streamline underwriting, and build stronger relationships with professional brokers.
                    </p>

                    <!-- Benefits List -->
                    <div class="space-y-4 mb-8">
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="ml-3 text-gray-700"><strong>Know Your Source:</strong> Instantly see which ISO sent each deal — no guesswork</p>
                        </div>
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="ml-3 text-gray-700"><strong>Faster Underwriting:</strong> Organized docs = faster decisions = more funded deals</p>
                        </div>
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="ml-3 text-gray-700"><strong>Attract Top ISOs:</strong> Professional lenders attract professional brokers</p>
                        </div>
                    </div>

                    <!-- Example Visual -->
                    <div class="bg-white rounded-xl p-4 border border-blue-200 shadow-sm">
                        <p class="text-xs text-gray-500 mb-3 font-medium uppercase tracking-wide">Example: Bank Statement with Diagonal Watermark</p>
                        <div class="bg-white rounded-lg border border-gray-300 aspect-[4/3] relative overflow-hidden shadow-inner">
                            <!-- Bank Statement Content -->
                            <div class="p-3 text-xs">
                                <!-- Bank Header -->
                                <div class="flex justify-between items-start mb-3 border-b border-gray-200 pb-2">
                                    <div>
                                        <div class="font-bold text-blue-800 text-sm">FIRST NATIONAL BANK</div>
                                        <div class="text-gray-500 text-[10px]">Account Statement</div>
                                    </div>
                                    <div class="text-right text-[10px] text-gray-600">
                                        <div>Account: ****4521</div>
                                        <div>Dec 1 - Dec 31, 2024</div>
                                    </div>
                                </div>
                                <!-- Account Summary -->
                                <div class="grid grid-cols-3 gap-2 mb-3 text-[9px]">
                                    <div class="bg-gray-50 p-1.5 rounded">
                                        <div class="text-gray-500">Beginning Bal</div>
                                        <div class="font-semibold text-gray-800">$24,521.33</div>
                                    </div>
                                    <div class="bg-gray-50 p-1.5 rounded">
                                        <div class="text-gray-500">Total Deposits</div>
                                        <div class="font-semibold text-green-600">+$18,450.00</div>
                                    </div>
                                    <div class="bg-gray-50 p-1.5 rounded">
                                        <div class="text-gray-500">Ending Bal</div>
                                        <div class="font-semibold text-gray-800">$31,284.58</div>
                                    </div>
                                </div>
                                <!-- Transactions -->
                                <div class="text-[8px] text-gray-600">
                                    <div class="flex justify-between py-0.5 border-b border-gray-100">
                                        <span>12/05 DEPOSIT - ACH PAYMENT</span>
                                        <span class="text-green-600">+$8,250.00</span>
                                    </div>
                                    <div class="flex justify-between py-0.5 border-b border-gray-100">
                                        <span>12/12 WIRE TRANSFER IN</span>
                                        <span class="text-green-600">+$5,200.00</span>
                                    </div>
                                    <div class="flex justify-between py-0.5 border-b border-gray-100">
                                        <span>12/18 CHECK DEPOSIT #1042</span>
                                        <span class="text-green-600">+$5,000.00</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Single Diagonal Watermark -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="transform -rotate-45 text-gray-400/40 font-bold text-base whitespace-nowrap tracking-wider">
                                    ISO: ABC Partners | Lender: Your Company
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-rose-600 mt-2 text-center font-medium">Diagonal watermark for complete protection</p>
                    </div>
                </div>

                <!-- ISO Use Case -->
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl p-8 lg:p-10 border border-emerald-100">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg shadow-emerald-500/30">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">For ISOs & Brokers</h3>
                            <p class="text-emerald-600 font-medium">Sales Teams & Partners</p>
                        </div>
                    </div>

                    <p class="text-gray-600 mb-6">
                        Stand out as a professional ISO. Send organized submissions that lenders love. Get proper credit for your deals. Build lasting relationships with multiple lenders.
                    </p>

                    <!-- Benefits List -->
                    <div class="space-y-4 mb-8">
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="ml-3 text-gray-700"><strong>Get Proper Credit:</strong> Your ISO on every page ensures lenders know it's your deal</p>
                        </div>
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="ml-3 text-gray-700"><strong>Shop Multiple Lenders:</strong> Same docs to 5 lenders — each copy marked for that specific lender</p>
                        </div>
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="ml-3 text-gray-700"><strong>Look Professional:</strong> Watermarked submissions show lenders you're serious</p>
                        </div>
                    </div>

                    <!-- Example Visual -->
                    <div class="bg-white rounded-xl p-4 border border-emerald-200 shadow-sm">
                        <p class="text-xs text-gray-500 mb-3 font-medium uppercase tracking-wide">Example: MCA Application with Diagonal Watermark</p>
                        <div class="bg-white rounded-lg border border-gray-300 aspect-[4/3] relative overflow-hidden shadow-inner">
                            <!-- Application Content -->
                            <div class="p-3 text-xs">
                                <!-- Header -->
                                <div class="text-center mb-3 border-b border-gray-200 pb-2">
                                    <div class="font-bold text-gray-800 text-sm">MERCHANT CASH ADVANCE APPLICATION</div>
                                    <div class="text-gray-500 text-[10px]">Business Funding Request Form</div>
                                </div>
                                <!-- Form Fields -->
                                <div class="space-y-2 text-[9px]">
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <div class="text-gray-500 mb-0.5">Business Name</div>
                                            <div class="border-b border-gray-300 pb-0.5 text-gray-700">ABC Retail LLC</div>
                                        </div>
                                        <div>
                                            <div class="text-gray-500 mb-0.5">DBA</div>
                                            <div class="border-b border-gray-300 pb-0.5 text-gray-700">ABC Store</div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <div class="text-gray-500 mb-0.5">Amount Requested</div>
                                            <div class="border-b border-gray-300 pb-0.5 text-gray-700 font-semibold">$75,000</div>
                                        </div>
                                        <div>
                                            <div class="text-gray-500 mb-0.5">Monthly Revenue</div>
                                            <div class="border-b border-gray-300 pb-0.5 text-gray-700">$42,500</div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500 mb-0.5">Business Address</div>
                                        <div class="border-b border-gray-300 pb-0.5 text-gray-700">123 Main St, Suite 100, New York, NY</div>
                                    </div>
                                </div>
                            </div>
                            <!-- Single Diagonal Watermark -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="transform -rotate-45 text-emerald-600/30 font-bold text-base whitespace-nowrap tracking-wider">
                                    ISO: ABC Partners | Lender: FastFund
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-rose-600 mt-2 text-center font-medium">Diagonal watermark for complete protection</p>
                    </div>
                </div>
            </div>

            <!-- Why Both Sides Win -->
            <div class="mt-20">
                <div class="text-center max-w-3xl mx-auto mb-12">
                    <span class="inline-block px-4 py-1.5 bg-green-100 text-green-700 text-sm font-semibold rounded-full mb-4">Why It Works</span>
                    <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">
                        When Everyone Watermarks, Everyone Wins
                    </h3>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <!-- ISOs Win -->
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl p-8 border border-emerald-200">
                        <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            ISOs Win
                        </h4>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-gray-900 font-semibold">Clear Deal Attribution</p>
                                    <p class="text-gray-600 text-sm">Your ISO is on every document. Lenders always know who brought the deal. No confusion about commissions.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-gray-900 font-semibold">Stronger Lender Relationships</p>
                                    <p class="text-gray-600 text-sm">Professional submissions get priority. Lenders respond faster to organized ISOs they can trust.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-gray-900 font-semibold">More Funded Deals</p>
                                    <p class="text-gray-600 text-sm">Clean submissions move faster through underwriting. Faster funding = happier merchants = more referrals.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lenders Win -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-3xl p-8 border border-blue-200">
                        <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Lenders Win
                        </h4>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-gray-900 font-semibold">Organized Deal Flow</p>
                                    <p class="text-gray-600 text-sm">Know exactly who sent each deal. No more sorting through mystery submissions or duplicate merchants.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-gray-900 font-semibold">Better ISO Partners</p>
                                    <p class="text-gray-600 text-sm">When you require watermarking, you attract professional ISOs who run organized operations.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-gray-900 font-semibold">Faster Decisions</p>
                                    <p class="text-gray-600 text-sm">Clear documentation means less back-and-forth. Fund more deals in less time with less hassle.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Line -->
                <div class="mt-12 bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl p-8 text-center">
                    <p class="text-xl md:text-2xl font-bold text-white mb-2">
                        Professional ISOs + Professional Lenders = More Funded Deals
                    </p>
                    <p class="text-primary-100">
                        Watermarking is the new standard. Join the ISOs and lenders who are already winning.
                    </p>
                </div>
            </div>

            <!-- Industry Standard Note -->
            <div class="mt-16 text-center">
                <div class="inline-flex items-center px-6 py-3 bg-green-50 border border-green-200 rounded-full">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span class="text-green-800 font-medium">Leading ISOs & lenders already watermark every PDF — join them.</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Security Section -->
    <section id="security" class="py-24 bg-dark-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Content -->
                <div>
                    <span class="inline-block px-4 py-1.5 bg-primary-900/50 border border-primary-700/50 text-primary-400 text-sm font-semibold rounded-full mb-6">Security First</span>
                    <h2 class="text-3xl sm:text-4xl font-bold mb-6">
                        Your documents are safe with us
                    </h2>
                    <p class="text-gray-400 text-lg mb-8">
                        We take security seriously. Your sensitive financial documents are protected at every step of the process.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-1">End-to-End Encryption</h4>
                                <p class="text-gray-400 text-sm">All file transfers are encrypted using 256-bit SSL/TLS encryption.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-1">Automatic File Deletion</h4>
                                <p class="text-gray-400 text-sm">Files are automatically deleted after {{ config('watermark.file_retention_days', 7) }} days. No data retention.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-1">Secure Infrastructure</h4>
                                <p class="text-gray-400 text-sm">Hosted on enterprise-grade servers with regular security audits.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-1">Private Processing</h4>
                                <p class="text-gray-400 text-sm">Your files are never shared or accessed by third parties.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visual -->
                <div class="relative">
                    <div class="bg-gradient-to-br from-dark-700 to-dark-800 rounded-2xl p-8 border border-dark-700">
                        <div class="bg-dark-900 rounded-xl p-6 border border-dark-700">
                            <!-- Mock Document Preview - Bank Statement -->
                            <div class="aspect-[8.5/11] bg-white rounded-lg p-4 relative overflow-hidden">
                                <!-- Bank Statement Content -->
                                <div class="text-[7px] text-gray-700">
                                    <!-- Bank Header -->
                                    <div class="flex justify-between items-start mb-3 border-b border-gray-200 pb-2">
                                        <div>
                                            <div class="font-bold text-blue-800 text-[10px]">FIRST NATIONAL BANK</div>
                                            <div class="text-gray-500 text-[6px]">Business Checking Account Statement</div>
                                        </div>
                                        <div class="text-right text-[6px] text-gray-500">
                                            <div>Account: ****4521</div>
                                            <div>Statement Period: Dec 2024</div>
                                        </div>
                                    </div>
                                    <!-- Summary Boxes -->
                                    <div class="grid grid-cols-3 gap-1 mb-3">
                                        <div class="bg-gray-50 p-1 rounded text-center">
                                            <div class="text-[5px] text-gray-500">Beginning</div>
                                            <div class="font-bold text-[7px]">$24,521</div>
                                        </div>
                                        <div class="bg-gray-50 p-1 rounded text-center">
                                            <div class="text-[5px] text-gray-500">Deposits</div>
                                            <div class="font-bold text-[7px] text-green-600">+$18,450</div>
                                        </div>
                                        <div class="bg-gray-50 p-1 rounded text-center">
                                            <div class="text-[5px] text-gray-500">Ending</div>
                                            <div class="font-bold text-[7px]">$31,284</div>
                                        </div>
                                    </div>
                                    <!-- Transaction Table Header -->
                                    <div class="flex justify-between text-[5px] font-bold text-gray-500 border-b border-gray-300 pb-0.5 mb-1">
                                        <span>DATE</span>
                                        <span>DESCRIPTION</span>
                                        <span>AMOUNT</span>
                                    </div>
                                    <!-- Transactions -->
                                    <div class="space-y-0.5 text-[5px]">
                                        <div class="flex justify-between border-b border-gray-100 pb-0.5">
                                            <span class="w-8">12/05</span>
                                            <span class="flex-1 px-1">ACH DEPOSIT - SALES</span>
                                            <span class="text-green-600">+$8,250.00</span>
                                        </div>
                                        <div class="flex justify-between border-b border-gray-100 pb-0.5">
                                            <span class="w-8">12/08</span>
                                            <span class="flex-1 px-1">WIRE TRANSFER IN</span>
                                            <span class="text-green-600">+$5,200.00</span>
                                        </div>
                                        <div class="flex justify-between border-b border-gray-100 pb-0.5">
                                            <span class="w-8">12/12</span>
                                            <span class="flex-1 px-1">CHECK DEP #1042</span>
                                            <span class="text-green-600">+$5,000.00</span>
                                        </div>
                                        <div class="flex justify-between border-b border-gray-100 pb-0.5">
                                            <span class="w-8">12/15</span>
                                            <span class="flex-1 px-1">UTILITIES PAYMENT</span>
                                            <span class="text-red-500">-$842.50</span>
                                        </div>
                                        <div class="flex justify-between border-b border-gray-100 pb-0.5">
                                            <span class="w-8">12/18</span>
                                            <span class="flex-1 px-1">PAYROLL - ADP</span>
                                            <span class="text-red-500">-$4,200.00</span>
                                        </div>
                                        <div class="flex justify-between border-b border-gray-100 pb-0.5">
                                            <span class="w-8">12/22</span>
                                            <span class="flex-1 px-1">CREDIT CARD SALES</span>
                                            <span class="text-green-600">+$3,156.08</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Single Diagonal Watermark - Large -->
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden">
                                    <div class="transform -rotate-45 text-gray-400/30 font-bold whitespace-nowrap" style="font-size: clamp(12px, 5vw, 22px); letter-spacing: 0.1em;">
                                        ISO: ABC Partners | Lender: FastFund
                                    </div>
                                </div>
                                <!-- QR Code in bottom-right -->
                                <div class="absolute bottom-2 right-2 z-20">
                                    <div class="w-12 h-12 bg-white border border-gray-300 rounded p-1 shadow-sm">
                                        <svg viewBox="0 0 100 100" class="w-full h-full">
                                            <!-- Top-left finder pattern -->
                                            <rect x="0" y="0" width="28" height="28" fill="#1f2937"/>
                                            <rect x="4" y="4" width="20" height="20" fill="white"/>
                                            <rect x="8" y="8" width="12" height="12" fill="#1f2937"/>
                                            <!-- Top-right finder pattern -->
                                            <rect x="72" y="0" width="28" height="28" fill="#1f2937"/>
                                            <rect x="76" y="4" width="20" height="20" fill="white"/>
                                            <rect x="80" y="8" width="12" height="12" fill="#1f2937"/>
                                            <!-- Bottom-left finder pattern -->
                                            <rect x="0" y="72" width="28" height="28" fill="#1f2937"/>
                                            <rect x="4" y="76" width="20" height="20" fill="white"/>
                                            <rect x="8" y="80" width="12" height="12" fill="#1f2937"/>
                                            <!-- Data modules -->
                                            <rect x="36" y="4" width="6" height="6" fill="#1f2937"/>
                                            <rect x="48" y="4" width="6" height="6" fill="#1f2937"/>
                                            <rect x="36" y="16" width="6" height="6" fill="#1f2937"/>
                                            <rect x="54" y="16" width="6" height="6" fill="#1f2937"/>
                                            <rect x="4" y="36" width="6" height="6" fill="#1f2937"/>
                                            <rect x="16" y="42" width="6" height="6" fill="#1f2937"/>
                                            <rect x="36" y="36" width="6" height="6" fill="#1f2937"/>
                                            <rect x="48" y="42" width="6" height="6" fill="#1f2937"/>
                                            <rect x="60" y="36" width="6" height="6" fill="#1f2937"/>
                                            <rect x="72" y="42" width="6" height="6" fill="#1f2937"/>
                                            <rect x="84" y="36" width="6" height="6" fill="#1f2937"/>
                                            <rect x="42" y="54" width="6" height="6" fill="#1f2937"/>
                                            <rect x="60" y="54" width="6" height="6" fill="#1f2937"/>
                                            <rect x="78" y="54" width="6" height="6" fill="#1f2937"/>
                                            <rect x="36" y="66" width="6" height="6" fill="#1f2937"/>
                                            <rect x="54" y="66" width="6" height="6" fill="#1f2937"/>
                                            <rect x="72" y="72" width="6" height="6" fill="#1f2937"/>
                                            <rect x="84" y="78" width="6" height="6" fill="#1f2937"/>
                                            <rect x="36" y="84" width="6" height="6" fill="#1f2937"/>
                                            <rect x="54" y="90" width="6" height="6" fill="#1f2937"/>
                                            <rect x="72" y="84" width="6" height="6" fill="#1f2937"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between text-sm">
                            <span class="text-gray-500">bank-statement-dec2024.pdf</span>
                            <span class="text-green-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Protected + Tracked
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 bg-gradient-to-r from-primary-600 to-primary-700">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">
                Ready to Join the New Standard?
            </h2>
            <p class="text-xl text-primary-100 mb-10">
                ISOs get proper credit. Lenders get organized deals. Everyone closes faster.
            </p>
            @guest
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white hover:bg-gray-100 text-primary-700 font-semibold rounded-xl transition-all duration-200 shadow-xl hover:-translate-y-0.5">
                <span>Start Free Today</span>
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            @else
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white hover:bg-gray-100 text-primary-700 font-semibold rounded-xl transition-all duration-200 shadow-xl hover:-translate-y-0.5">
                <span>Go to Dashboard</span>
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            @endguest
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <!-- Brand -->
                <div class="md:col-span-2">
                    <a href="/" class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-white">Blu<span class="text-primary-400">Mark</span></span>
                    </a>
                    <p class="text-sm max-w-md">
                        PDF watermarking built for the MCA industry. Dual watermarks help ISOs get proper credit and lenders get organized submissions. Better for everyone.
                    </p>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#features" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="#how-it-works" class="hover:text-white transition-colors">How It Works</a></li>
                        <li><a href="#use-cases" class="hover:text-white transition-colors">Use Cases</a></li>
                        <li><a href="#security" class="hover:text-white transition-colors">Security</a></li>
                        <li><a href="/docs/api" class="hover:text-white transition-colors">Developers</a></li>
                    </ul>
                </div>

                <!-- Account -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Account</h4>
                    <ul class="space-y-2 text-sm">
                        @guest
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Sign In</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Create Account</a></li>
                        @else
                        <li><a href="{{ route('dashboard') }}" class="hover:text-white transition-colors">Dashboard</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="hover:text-white transition-colors">My Jobs</a></li>
                        @endguest
                    </ul>
                </div>
            </div>

            <div class="border-t border-dark-700 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm">&copy; {{ date('Y') }} BluMark. All rights reserved.</p>
                <div class="flex space-x-6 mt-4 md:mt-0 text-sm">
                    <a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="{{ route('terms') }}" class="hover:text-white transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll-based navbar background -->
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('bg-dark-900/95', 'backdrop-blur-md', 'shadow-lg');
            } else {
                navbar.classList.remove('bg-dark-900/95', 'backdrop-blur-md', 'shadow-lg');
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
