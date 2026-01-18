<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Two-Factor Authentication</h1>
        <p class="text-gray-500 mt-1">Enter your verification code to continue</p>
    </div>

    <div x-data="{ useRecoveryCode: false }">
        <!-- TOTP Code Form -->
        <form method="POST" action="{{ route('two-factor.challenge') }}" x-show="!useRecoveryCode">
            @csrf

            <div>
                <label for="code" class="block font-medium text-sm text-gray-700 mb-1">Authentication Code</label>
                <input id="code" type="text" name="code" autofocus autocomplete="one-time-code"
                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 px-4 py-3 border text-sm transition-colors font-mono text-lg tracking-widest text-center"
                       placeholder="000000"
                       maxlength="6"
                       pattern="[0-9]{6}"
                       inputmode="numeric">
                @error('code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-primary-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200 shadow-lg shadow-primary-600/25">
                    Verify
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </button>
            </div>
        </form>

        <!-- Recovery Code Form -->
        <form method="POST" action="{{ route('two-factor.challenge') }}" x-show="useRecoveryCode" x-cloak>
            @csrf

            <div>
                <label for="recovery_code" class="block font-medium text-sm text-gray-700 mb-1">Recovery Code</label>
                <input id="recovery_code" type="text" name="recovery_code" autocomplete="off"
                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 px-4 py-3 border text-sm transition-colors font-mono"
                       placeholder="xxxxx-xxxxx">
                @error('recovery_code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-primary-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200 shadow-lg shadow-primary-600/25">
                    Verify
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </button>
            </div>
        </form>

        <!-- Toggle Link -->
        <div class="mt-6 text-center">
            <button type="button" @click="useRecoveryCode = !useRecoveryCode" class="text-sm text-primary-600 hover:text-primary-500">
                <span x-show="!useRecoveryCode">Use a recovery code instead</span>
                <span x-show="useRecoveryCode" x-cloak>Use authentication code instead</span>
            </button>
        </div>
    </div>

    <!-- Back to login -->
    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700 inline-flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to login
        </a>
    </div>

    <!-- Alpine.js for toggle (if not already included) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</x-guest-layout>
