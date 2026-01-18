<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Two-Factor Authentication</h1>
            <p class="text-gray-500 mt-1">Add additional security to your account using two-factor authentication.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium text-green-800">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium text-red-800">{{ $errors->first() }}</span>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-center">
                    @if($enabled)
                        <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Two-factor authentication is enabled</h3>
                            <p class="text-sm text-gray-500">Your account is protected with an authenticator app.</p>
                        </div>
                    @else
                        <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Two-factor authentication is not enabled</h3>
                            <p class="text-sm text-gray-500">Add extra security to your account by enabling 2FA.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="p-6">
                @if($enabled)
                    <p class="text-gray-600 mb-6">
                        When two-factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's authenticator application.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('two-factor.recovery-codes') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-medium text-sm hover:bg-gray-200 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            View Recovery Codes
                        </a>

                        <button type="button" onclick="document.getElementById('disable-modal').classList.remove('hidden')" class="inline-flex items-center justify-center px-5 py-2.5 bg-red-100 text-red-700 rounded-xl font-medium text-sm hover:bg-red-200 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            Disable 2FA
                        </button>
                    </div>
                @else
                    <p class="text-gray-600 mb-6">
                        When two-factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's authenticator application like Google Authenticator or Authy.
                    </p>

                    <a href="{{ route('two-factor.enable') }}" class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white rounded-xl font-medium text-sm hover:bg-primary-700 transition-colors shadow-lg shadow-primary-600/25">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Enable Two-Factor Authentication
                    </a>
                @endif
            </div>
        </div>

        <!-- Back to Profile -->
        <div class="mt-6">
            <a href="{{ route('profile.show') }}" class="text-sm text-primary-600 hover:text-primary-500 inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Profile
            </a>
        </div>
    </div>

    <!-- Disable 2FA Modal -->
    @if($enabled)
    <div id="disable-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Disable Two-Factor Authentication</h3>
            <p class="text-gray-600 mb-6">Please confirm your password to disable two-factor authentication.</p>

            <form method="POST" action="{{ route('two-factor.disable') }}">
                @csrf
                @method('DELETE')

                <div class="mb-4">
                    <label for="password" class="block font-medium text-sm text-gray-700 mb-1">Password</label>
                    <input id="password" type="password" name="password" required
                           class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 px-4 py-3 border text-sm transition-colors"
                           placeholder="Enter your password">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('disable-modal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 text-white rounded-xl font-medium text-sm hover:bg-red-700 transition-colors">
                        Disable 2FA
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</x-app-layout>
