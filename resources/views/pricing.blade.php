<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pricing - {{ config('app.name', 'BluMark') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

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
                        }
                    },
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-dark-900 border-b border-dark-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-2">
                        <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-white">Blu<span class="text-primary-400">Mark</span></span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-300 hover:text-white text-sm font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white text-sm font-medium">Sign in</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-primary-700 transition-colors">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="bg-dark-900 py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl sm:text-5xl font-bold text-white mb-4">
                Simple, Transparent <span class="text-primary-400">Pricing</span>
            </h1>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                Choose the plan that fits your needs. Start free and upgrade as you grow.
            </p>
        </div>
    </div>

    <!-- Pricing Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 pb-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($plans as $plan)
                <div class="bg-white rounded-2xl shadow-xl border {{ $plan->slug === 'pro' ? 'border-primary-500 ring-2 ring-primary-500' : 'border-gray-100' }} overflow-hidden relative">
                    @if($plan->slug === 'pro')
                        <div class="absolute top-0 left-0 right-0 bg-primary-500 text-white text-xs font-semibold text-center py-1">
                            MOST POPULAR
                        </div>
                    @endif
                    <div class="p-6 {{ $plan->slug === 'pro' ? 'pt-10' : '' }}">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $plan->name }}</h3>
                        <div class="mt-4 flex items-baseline">
                            <span class="text-4xl font-bold text-gray-900">{{ $plan->getMonthlyPrice() }}</span>
                            @if(!$plan->isFree())
                                <span class="ml-1 text-gray-500">/month</span>
                            @endif
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            @if($plan->jobs_limit)
                                {{ $plan->jobs_limit }} jobs/month
                            @else
                                Unlimited jobs
                            @endif
                        </p>

                        <ul class="mt-6 space-y-3">
                            @foreach($plan->features ?? [] as $feature)
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-primary-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm text-gray-600">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-8">
                            @auth
                                @if($plan->isFree())
                                    <span class="block w-full text-center px-4 py-3 bg-gray-100 rounded-xl text-sm font-semibold text-gray-500">
                                        Current Plan
                                    </span>
                                @else
                                    <a href="{{ route('billing.subscription.checkout', $plan) }}"
                                       class="block w-full text-center px-4 py-3 {{ $plan->slug === 'pro' ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-gray-900 hover:bg-gray-800 text-white' }} rounded-xl text-sm font-semibold transition-colors">
                                        Subscribe Now
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('register') }}"
                                   class="block w-full text-center px-4 py-3 {{ $plan->slug === 'pro' ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-gray-900 hover:bg-gray-800 text-white' }} rounded-xl text-sm font-semibold transition-colors">
                                    Get Started
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Credit Packs Section -->
    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">Need More Jobs?</h2>
                <p class="mt-4 text-lg text-gray-600">Purchase credit packs for extra watermarking jobs when you need them.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                @foreach($creditPacks as $pack)
                    <div class="bg-gray-50 rounded-2xl p-6 border {{ $pack->is_popular ? 'border-primary-500' : 'border-gray-200' }} relative">
                        @if($pack->is_popular)
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                                <span class="bg-primary-500 text-white text-xs font-semibold px-3 py-1 rounded-full">BEST VALUE</span>
                            </div>
                        @endif
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $pack->name }}</h3>
                            <div class="mt-4">
                                <span class="text-3xl font-bold text-gray-900">{{ $pack->getPriceFormatted() }}</span>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">{{ $pack->getTotalCredits() }} credits</p>
                            <p class="text-xs text-gray-400">${{ number_format($pack->getPricePerCredit(), 2) }} per credit</p>

                            @auth
                                <a href="{{ route('billing.credits.purchase', $pack) }}"
                                   class="mt-6 block w-full text-center px-4 py-2.5 {{ $pack->is_popular ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-gray-900 hover:bg-gray-800 text-white' }} rounded-xl text-sm font-semibold transition-colors">
                                    Buy Now
                                </a>
                            @else
                                <a href="{{ route('register') }}"
                                   class="mt-6 block w-full text-center px-4 py-2.5 {{ $pack->is_popular ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-gray-900 hover:bg-gray-800 text-white' }} rounded-xl text-sm font-semibold transition-colors">
                                    Sign Up to Buy
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-gray-50 py-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Frequently Asked Questions</h2>

            <div class="space-y-6">
                <div class="bg-white rounded-xl p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">What counts as a job?</h3>
                    <p class="mt-2 text-gray-600">One job is one PDF file that you watermark. The number of pages in the PDF doesn't affect the job count, but each plan has a page limit per job.</p>
                </div>
                <div class="bg-white rounded-xl p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Can I change my plan later?</h3>
                    <p class="mt-2 text-gray-600">Yes! You can upgrade or downgrade your plan at any time. Changes take effect immediately, and we'll prorate the difference.</p>
                </div>
                <div class="bg-white rounded-xl p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Do credits expire?</h3>
                    <p class="mt-2 text-gray-600">No, credits never expire. Use them whenever you need extra watermarking jobs beyond your monthly plan limit.</p>
                </div>
                <div class="bg-white rounded-xl p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">What payment methods do you accept?</h3>
                    <p class="mt-2 text-gray-600">We accept all major credit cards (Visa, MasterCard, American Express) through our secure payment processor, Stripe.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark-900 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-2 mb-4 md:mb-0">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold">Blu<span class="text-primary-400">Mark</span></span>
                </div>
                <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} BluMark. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
