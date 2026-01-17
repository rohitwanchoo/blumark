<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <a href="{{ route('billing.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Billing
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Payment Methods</h1>
            <p class="text-gray-500 mt-1">Manage your payment methods</p>
        </div>

        <!-- Existing Payment Methods -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Your Cards</h3>
            </div>
            <div class="p-6">
                @if($paymentMethods->isEmpty())
                    <div class="text-center py-8">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">No payment methods added yet</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($paymentMethods as $method)
                            <div class="flex items-center justify-between p-4 rounded-xl border border-gray-200">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            {{ ucfirst($method->card->brand) }} ending in {{ $method->card->last4 }}
                                        </p>
                                        <p class="text-sm text-gray-500">Expires {{ $method->card->exp_month }}/{{ $method->card->exp_year }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    @if($defaultPaymentMethod && $defaultPaymentMethod->id === $method->id)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-700">
                                            Default
                                        </span>
                                    @else
                                        <form action="{{ route('billing.payment-methods.default', $method->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                                Set Default
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('billing.payment-methods.destroy', $method->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this payment method?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Add Payment Method -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Add Payment Method</h3>
            </div>
            <div class="p-6">
                <form id="payment-form">
                    <div id="card-element" class="p-4 border border-gray-200 rounded-xl"></div>
                    <div id="card-errors" class="mt-2 text-sm text-red-600" role="alert"></div>
                    <button type="submit" id="submit-button" class="mt-4 w-full inline-flex justify-center items-center px-4 py-3 bg-primary-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                        Add Card
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ config('cashier.key') }}');
        const elements = stripe.elements();
        const cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#1f2937',
                    '::placeholder': {
                        color: '#9ca3af',
                    },
                },
            },
        });

        cardElement.mount('#card-element');

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const cardErrors = document.getElementById('card-errors');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';

            const { setupIntent, error } = await stripe.confirmCardSetup(
                '{{ $intent->client_secret }}',
                {
                    payment_method: {
                        card: cardElement,
                    }
                }
            );

            if (error) {
                cardErrors.textContent = error.message;
                submitButton.disabled = false;
                submitButton.textContent = 'Add Card';
            } else {
                // Submit to server
                const formData = new FormData();
                formData.append('payment_method', setupIntent.payment_method);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route('billing.payment-methods.store') }}', {
                    method: 'POST',
                    body: formData,
                }).then(response => {
                    window.location.reload();
                });
            }
        });
    </script>
</x-app-layout>
