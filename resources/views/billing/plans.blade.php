<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <a href="{{ route('billing.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Billing
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Choose Your Plan</h1>
            <p class="text-gray-500 mt-1">Select the plan that best fits your watermarking needs</p>
        </div>

        <!-- Plan Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($plans as $plan)
                <div class="bg-white rounded-2xl shadow-sm border {{ $plan->slug === 'pro' ? 'border-primary-500 ring-2 ring-primary-500' : 'border-gray-100' }} overflow-hidden relative flex flex-col">
                    @if($plan->slug === 'pro')
                        <div class="absolute top-0 left-0 right-0 bg-primary-500 text-white text-xs font-semibold text-center py-1">
                            MOST POPULAR
                        </div>
                    @endif
                    <div class="p-6 {{ $plan->slug === 'pro' ? 'pt-10' : '' }} flex flex-col flex-grow">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $plan->name }}</h3>
                        <div class="mt-4 flex items-baseline">
                            <span class="text-4xl font-bold text-gray-900">{{ $plan->getMonthlyPrice() }}</span>
                            @if(!$plan->isFree())
                                <span class="ml-1 text-gray-500">/month</span>
                            @endif
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            @if($plan->jobs_limit)
                                {{ $plan->jobs_limit }} watermark jobs/month
                            @else
                                Unlimited watermark jobs
                            @endif
                        </p>

                        <ul class="mt-6 space-y-3 flex-grow">
                            @foreach($plan->features ?? [] as $feature)
                                @php
                                    $isExcluded = str_starts_with($feature, '~');
                                    $featureText = $isExcluded ? substr($feature, 1) : $feature;
                                @endphp
                                <li class="flex items-start">
                                    @if($isExcluded)
                                        <svg class="w-5 h-5 text-gray-300 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-sm text-gray-400">{{ $featureText }}</span>
                                    @else
                                        <svg class="w-5 h-5 text-primary-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-sm text-gray-600">{{ $featureText }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-auto pt-6">
                            @if($currentPlan?->id === $plan->id)
                                <span class="block w-full text-center px-4 py-3 bg-gray-100 rounded-xl text-sm font-semibold text-gray-500">
                                    Current Plan
                                </span>
                            @elseif($plan->isFree())
                                <form action="{{ route('billing.subscription.cancel') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="block w-full text-center px-4 py-3 bg-gray-200 hover:bg-gray-300 rounded-xl text-sm font-semibold text-gray-700 transition-colors">
                                        Downgrade to Free
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('billing.subscription.checkout', $plan) }}"
                                   class="block w-full text-center px-4 py-3 {{ $plan->slug === 'pro' ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-gray-900 hover:bg-gray-800 text-white' }} rounded-xl text-sm font-semibold transition-colors">
                                    @if($currentPlan && !$currentPlan->isFree() && $plan->price_cents > $currentPlan->price_cents)
                                        Upgrade
                                    @elseif($currentPlan && !$currentPlan->isFree())
                                        Switch Plan
                                    @else
                                        Subscribe
                                    @endif
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- FAQ -->
        <div class="mt-12 max-w-3xl">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Plan FAQs</h2>
            <div class="space-y-4">
                <div class="bg-white rounded-xl p-4 border border-gray-100">
                    <h3 class="font-medium text-gray-900">Can I change plans at any time?</h3>
                    <p class="mt-1 text-sm text-gray-600">Yes, you can upgrade or downgrade at any time. Changes take effect immediately.</p>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-100">
                    <h3 class="font-medium text-gray-900">What happens to unused watermark jobs?</h3>
                    <p class="mt-1 text-sm text-gray-600">Monthly allocations reset at the start of each billing period and don't roll over.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
