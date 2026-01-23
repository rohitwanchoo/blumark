<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('smtp-settings.index') }}" 
                   class="inline-flex items-center text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Add SMTP Settings</h1>
            </div>
            <p class="text-gray-500">Configure your custom SMTP server for sending emails</p>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="ml-3 text-sm text-blue-700">
                    <p class="mb-2">You'll need SMTP credentials from your email provider. Common ports are 587 (TLS) or 465 (SSL).</p>
                    <p class="font-semibold">💡 For Gmail users: <a href="{{ route('smtp-settings.providers') }}" class="underline">Use the simple setup instead</a> - it's much easier!</p>
                </div>
            </div>
        </div>

        <!-- Gmail Help Box (shown when host contains gmail) -->
        <div id="gmail-help" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="ml-3 text-sm text-yellow-800">
                    <p class="font-semibold mb-1">Gmail Detected!</p>
                    <p class="mb-2">For Gmail, you must use an <strong>App Password</strong>, not your regular Gmail password.</p>
                    <ol class="list-decimal ml-4 space-y-1">
                        <li>Enable 2-Step Verification at <a href="https://myaccount.google.com/security" target="_blank" class="underline">Google Account Security</a></li>
                        <li>Create an App Password at <a href="https://myaccount.google.com/apppasswords" target="_blank" class="underline">Google App Passwords</a></li>
                        <li>Use that 16-character password in the "Password" field below</li>
                    </ol>
                    <p class="mt-2 italic">Or use our <a href="{{ route('smtp-settings.providers') }}" class="underline font-semibold">Simple Gmail Setup</a> for guided instructions!</p>
                </div>
            </div>
        </div>

        <script>
            // Show Gmail help when host contains "gmail"
            document.addEventListener('DOMContentLoaded', function() {
                const hostInput = document.getElementById('host');
                const gmailHelp = document.getElementById('gmail-help');

                function checkGmail() {
                    if (hostInput.value.toLowerCase().includes('gmail')) {
                        gmailHelp.classList.remove('hidden');
                    } else {
                        gmailHelp.classList.add('hidden');
                    }
                }

                hostInput.addEventListener('input', checkGmail);
                hostInput.addEventListener('change', checkGmail);
                checkGmail();
            });
        </script>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <form action="{{ route('smtp-settings.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Configuration Name
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="e.g., Gmail SMTP"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Host -->
                <div>
                    <label for="host" class="block text-sm font-medium text-gray-700 mb-2">
                        SMTP Host
                    </label>
                    <input type="text" 
                           name="host" 
                           id="host" 
                           value="{{ old('host') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="e.g., smtp.gmail.com"
                           required>
                    @error('host')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Port and Encryption -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="port" class="block text-sm font-medium text-gray-700 mb-2">
                            Port
                        </label>
                        <input type="number" 
                               name="port" 
                               id="port" 
                               value="{{ old('port', 587) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               required>
                        @error('port')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="encryption" class="block text-sm font-medium text-gray-700 mb-2">
                            Encryption
                        </label>
                        <select name="encryption" 
                                id="encryption"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                required>
                            <option value="tls" {{ old('encryption') == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="none" {{ old('encryption') == 'none' ? 'selected' : '' }}>None</option>
                        </select>
                        @error('encryption')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username
                    </label>
                    <input type="text" 
                           name="username" 
                           id="username" 
                           value="{{ old('username') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="your.email@example.com"
                           required>
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password / App Password
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="Enter your password"
                           required>
                    <p class="mt-1 text-xs text-gray-500">For Gmail/Google Workspace, use an App Password instead of your regular password.</p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- From Email -->
                <div>
                    <label for="from_email" class="block text-sm font-medium text-gray-700 mb-2">
                        From Email
                    </label>
                    <input type="email" 
                           name="from_email" 
                           id="from_email" 
                           value="{{ old('from_email') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="sender@example.com"
                           required>
                    @error('from_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- From Name -->
                <div>
                    <label for="from_name" class="block text-sm font-medium text-gray-700 mb-2">
                        From Name
                    </label>
                    <input type="text" 
                           name="from_name" 
                           id="from_name" 
                           value="{{ old('from_name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="Your Company Name"
                           required>
                    @error('from_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Active -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">
                        Set as active SMTP configuration
                    </label>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('smtp-settings.index') }}" 
                       class="px-4 py-2 text-gray-700 hover:text-gray-900 font-medium">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        Save SMTP Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
