<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Billing & Subscription</h1>
            <p class="text-gray-500 mt-1">Manage your subscription, credits, and payment methods</p>
        </div>

        <!-- Current Plan & Usage -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Current Plan Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Current Plan</h3>
                    @if(!$currentPlan?->isFree())
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-700">
                            Active
                        </span>
                    @endif
                </div>
                <div class="mb-4">
                    <p class="text-3xl font-bold text-gray-900">{{ $currentPlan?->name ?? 'Free' }}</p>
                    <p class="text-gray-500 mt-1">{{ $currentPlan?->getMonthlyPrice() ?? '$0' }}/month</p>
                </div>
                <a href="{{ route('billing.plans') }}" class="inline-flex items-center text-sm text-primary-600 hover:text-primary-700 font-medium">
                    {{ $currentPlan?->isFree() ? 'Upgrade Plan' : 'Manage Plan' }}
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <!-- Monthly Usage Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Usage</h3>
                <div class="mb-4">
                    <p class="text-3xl font-bold text-gray-900">{{ $monthlyUsage }}</p>
                    <p class="text-gray-500 mt-1">
                        @if($remainingJobs !== null)
                            of {{ $currentPlan?->jobs_limit ?? 0 }} used
                        @else
                            watermark jobs this month (unlimited)
                        @endif
                    </p>
                </div>
                @if($remainingJobs !== null && $currentPlan?->jobs_limit)
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-primary-600 h-2 rounded-full" style="width: {{ min(100, ($monthlyUsage / $currentPlan->jobs_limit) * 100) }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">{{ $remainingJobs }} watermark jobs remaining</p>
                @endif
            </div>

            <!-- Credits Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Available Credits</h3>
                <div class="mb-4">
                    <p class="text-3xl font-bold text-gray-900">{{ $credits }}</p>
                    <p class="text-gray-500 mt-1">credits available</p>
                </div>
                <a href="{{ route('billing.credits') }}" class="inline-flex items-center text-sm text-primary-600 hover:text-primary-700 font-medium">
                    Buy Credits
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Upgrade Plan -->
                    @if($currentPlan?->isFree())
                        <a href="{{ route('billing.plans') }}" class="flex items-center justify-between p-4 rounded-xl border border-gray-200 hover:border-primary-300 hover:bg-primary-50 transition-all group">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-4 group-hover:bg-primary-200">
                                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Upgrade Your Plan</p>
                                    <p class="text-sm text-gray-500">Get more watermark jobs and features</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endif

                    <!-- Buy Credits -->
                    <a href="{{ route('billing.credits') }}" class="flex items-center justify-between p-4 rounded-xl border border-gray-200 hover:border-primary-300 hover:bg-primary-50 transition-all group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4 group-hover:bg-green-200">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Buy Credit Pack</p>
                                <p class="text-sm text-gray-500">Get extra watermark jobs</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    <!-- Payment Methods -->
                    <a href="{{ route('billing.payment-methods') }}" class="flex items-center justify-between p-4 rounded-xl border border-gray-200 hover:border-primary-300 hover:bg-primary-50 transition-all group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4 group-hover:bg-purple-200">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Payment Methods</p>
                                <p class="text-sm text-gray-500">Manage your cards</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    <!-- Invoices -->
                    <a href="{{ route('billing.invoices') }}" class="flex items-center justify-between p-4 rounded-xl border border-gray-200 hover:border-primary-300 hover:bg-primary-50 transition-all group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-4 group-hover:bg-gray-200">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Invoices</p>
                                <p class="text-sm text-gray-500">Download billing history</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
                    <a href="{{ route('billing.credits') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">View all</a>
                </div>
                <div class="p-6">
                    @if($recentTransactions->isEmpty())
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500">No transactions yet</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($recentTransactions as $transaction)
                                <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 {{ $transaction->isCredit() ? 'bg-green-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center mr-3">
                                            @if($transaction->isCredit())
                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $transaction->description }}</p>
                                            <p class="text-xs text-gray-500">{{ $transaction->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-semibold {{ $transaction->isCredit() ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->getFormattedAmount() }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
