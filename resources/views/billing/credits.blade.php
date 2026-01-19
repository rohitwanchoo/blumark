<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <a href="{{ route('billing.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Billing
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Credits</h1>
            <p class="text-gray-500 mt-1">Purchase credit packs for extra submissions</p>
        </div>

        <!-- Info Box -->
        <div class="bg-primary-50 border border-primary-100 rounded-xl p-4 mb-8 flex items-start space-x-3">
            <svg class="w-5 h-5 text-primary-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-primary-800"><strong>1 credit = 1 watermark</strong> (single or multiple files at one time). Credits are used when you've reached your monthly plan limit. They never expire.</p>
        </div>

        <!-- Current Balance -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8 max-w-sm">
            <p class="text-sm font-medium text-gray-500 mb-1">Current Balance</p>
            <p class="text-4xl font-bold text-gray-900">{{ $userCredits }} <span class="text-lg font-normal text-gray-500">credits</span></p>
        </div>

        <!-- Credit Packs -->
        <div class="mb-12">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Buy Credits</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($creditPacks as $pack)
                    <div class="bg-white rounded-2xl border {{ $pack->is_popular ? 'border-primary-500 ring-2 ring-primary-500' : 'border-gray-100' }} shadow-sm p-6 relative">
                        @if($pack->is_popular)
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                                <span class="bg-primary-500 text-white text-xs font-semibold px-3 py-1 rounded-full">BEST VALUE</span>
                            </div>
                        @endif
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $pack->name }}</h3>
                            <div class="mt-4">
                                <span class="text-4xl font-bold text-gray-900">{{ $pack->getPriceFormatted() }}</span>
                            </div>
                            <p class="mt-2 text-lg font-medium text-gray-900">{{ $pack->getTotalCredits() }} watermarks</p>
                            <p class="text-sm text-gray-500">${{ number_format($pack->getPricePerCredit(), 2) }} per watermark</p>

                            @if($pack->bonus_credits > 0)
                                <p class="mt-2 text-sm text-green-600 font-medium">+{{ $pack->bonus_credits }} bonus watermarks!</p>
                            @endif

                            <a href="{{ route('billing.credits.purchase', $pack) }}"
                               class="mt-6 block w-full text-center px-4 py-3 {{ $pack->is_popular ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-gray-900 hover:bg-gray-800 text-white' }} rounded-xl text-sm font-semibold transition-colors">
                                Buy Now
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Transaction History -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Transaction History</h3>
            </div>
            <div class="p-6">
                @if($recentTransactions->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-medium text-gray-900 mb-1">No transactions yet</h4>
                        <p class="text-sm text-gray-500">Your credit transactions will appear here</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <th class="pb-3">Description</th>
                                    <th class="pb-3">Type</th>
                                    <th class="pb-3">Amount</th>
                                    <th class="pb-3">Balance</th>
                                    <th class="pb-3">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($recentTransactions as $transaction)
                                    <tr>
                                        <td class="py-4 text-sm text-gray-900">{{ $transaction->description }}</td>
                                        <td class="py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->type === 'purchase' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                        <td class="py-4 text-sm font-semibold {{ $transaction->isCredit() ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $transaction->getFormattedAmount() }}
                                        </td>
                                        <td class="py-4 text-sm text-gray-500">{{ $transaction->balance_after }}</td>
                                        <td class="py-4 text-sm text-gray-500">{{ $transaction->created_at->format('M j, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
