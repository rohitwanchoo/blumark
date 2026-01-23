<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('smtp-settings.index') }}" 
                   class="inline-flex items-center text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Connect Email Provider</h1>
            </div>
            <p class="text-gray-500">Choose your email provider to automatically configure SMTP settings</p>
        </div>

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Provider Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Gmail App Password Card - Simple option for non-technical users -->
            <div class="bg-white rounded-2xl border-2 border-blue-200 shadow-sm p-6 hover:shadow-md transition-shadow"
                 x-data="{ showInstructions: false }">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L5.455 4.64 12 9.548l6.545-4.91 1.528-1.145C21.69 2.28 24 3.434 24 5.457z"/>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Recommended
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Gmail (Simple Setup)</h3>
                <p class="text-sm text-gray-600 mb-4">Use your Gmail with an App Password. No technical setup needed.</p>

                <button @click="showInstructions = !showInstructions"
                        type="button"
                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors mb-3">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span x-text="showInstructions ? 'Hide Instructions' : 'Setup Gmail'"></span>
                </button>

                <!-- Instructions Panel -->
                <div x-show="showInstructions" x-cloak class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Easy Setup Steps
                    </h4>
                    <ol class="text-sm text-gray-700 space-y-3 mb-4">
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold mr-2">1</span>
                            <div>
                                <strong>Enable 2-Step Verification (REQUIRED)</strong><br>
                                <span class="text-gray-600">Go to <a href="https://myaccount.google.com/security" target="_blank" class="text-blue-600 hover:underline font-semibold">Google Account Security</a> and turn on 2-Step Verification. Click "Get Started" and follow the prompts to verify with your phone.</span>
                                <div class="mt-1 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs">
                                    ⚠️ <strong>Important:</strong> Without this, Step 2 won't work. You'll see "setting not available" error.
                                </div>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold mr-2">2</span>
                            <div>
                                <strong>Create App Password (Only after Step 1 is done)</strong><br>
                                <span class="text-gray-600">Visit <a href="https://myaccount.google.com/apppasswords" target="_blank" class="text-blue-600 hover:underline font-semibold">Google App Passwords</a>. Select "Mail" or "Other", type "Watermarking App", and click "Generate".</span>
                                <div class="mt-1 p-2 bg-blue-50 border border-blue-200 rounded text-xs">
                                    💡 <strong>Tip:</strong> You may need to sign in again. If you see "setting not available", go back to Step 1.
                                </div>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold mr-2">3</span>
                            <div>
                                <strong>Copy the Password</strong><br>
                                <span class="text-gray-600">Google will show you a 16-character password (like: <code class="bg-white px-1">abcd efgh ijkl mnop</code>). Copy it.</span>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold mr-2">4</span>
                            <div>
                                <strong>Configure Below</strong><br>
                                <span class="text-gray-600">Fill in the form below with your Gmail address and the app password you just created.</span>
                            </div>
                        </li>
                    </ol>

                    <form action="{{ route('smtp-settings.store') }}" method="POST" class="space-y-3 pt-3 border-t border-blue-200">
                        @csrf
                        <input type="hidden" name="provider" value="gmail">
                        <input type="hidden" name="provider_type" value="smtp">
                        <input type="hidden" name="host" value="smtp.gmail.com">
                        <input type="hidden" name="port" value="587">
                        <input type="hidden" name="encryption" value="tls">
                        <input type="hidden" name="name" value="Gmail (App Password)">

                        <div>
                            <label for="gmail_username" class="block text-sm font-medium text-gray-700 mb-1">
                                Your Gmail Address
                            </label>
                            <input type="email"
                                   name="username"
                                   id="gmail_username"
                                   placeholder="your.email@gmail.com"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   required>
                        </div>

                        <div>
                            <label for="gmail_app_password" class="block text-sm font-medium text-gray-700 mb-1">
                                App Password (16 characters)
                            </label>
                            <input type="password"
                                   name="password"
                                   id="gmail_app_password"
                                   placeholder="abcdefghijklmnop"
                                   maxlength="19"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-mono"
                                   required>
                            <p class="mt-1 text-xs text-gray-500">Paste the 16-character password from Step 2 (spaces don't matter)</p>
                        </div>

                        <div>
                            <label for="gmail_from_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Your Name / Company Name
                            </label>
                            <input type="text"
                                   name="from_name"
                                   id="gmail_from_name"
                                   placeholder="Your Company Name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   required>
                        </div>

                        <input type="hidden" name="from_email" id="gmail_from_email">

                        <div class="flex items-center">
                            <input type="checkbox"
                                   name="is_active"
                                   id="gmail_is_active"
                                   value="1"
                                   checked
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="gmail_is_active" class="ml-2 text-sm text-gray-700">
                                Set as active (use this for sending emails)
                            </label>
                        </div>

                        <button type="submit"
                                onclick="document.getElementById('gmail_from_email').value = document.getElementById('gmail_username').value;"
                                class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors text-sm">
                            Save Gmail Settings
                        </button>
                    </form>
                </div>
            </div>

            @foreach($providers as $key => $provider)
                @if($provider->getType() === 'oauth')
                    <!-- OAuth Provider Card -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                @if($key === 'gmail')
                                    <svg class="w-6 h-6 text-primary-600" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L5.455 4.64 12 9.548l6.545-4.91 1.528-1.145C21.69 2.28 24 3.434 24 5.457z"/>
                                    </svg>
                                @endif
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                OAuth
                            </span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            {{ $provider->getDisplayName() }}
                            @if($key === 'gmail')
                                <span class="text-sm font-normal text-gray-500">(OAuth)</span>
                            @endif
                        </h3>
                        <p class="text-sm text-gray-600 mb-6">
                            @if($key === 'gmail')
                                For advanced users. Requires Google Cloud setup.
                            @else
                                Connect securely using OAuth 2.0. No password needed.
                            @endif
                        </p>
                        <a href="{{ route('smtp-settings.provider.connect', $key) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Connect {{ $provider->getDisplayName() }}
                        </a>
                    </div>
                @else
                    <!-- API Key Provider Card -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition-shadow"
                         x-data="{ showForm: false }">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                API Key
                            </span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $provider->getDisplayName() }}</h3>
                        <p class="text-sm text-gray-600 mb-6">Configure using your API credentials.</p>
                        <button @click="showForm = !showForm" 
                                type="button"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Configure {{ $provider->getDisplayName() }}
                        </button>

                        <!-- Configuration Form -->
                        <div x-show="showForm" x-cloak class="mt-6 pt-6 border-t border-gray-200">
                            <form action="{{ route('smtp-settings.provider.api-key', $key) }}" method="POST" class="space-y-4">
                                @csrf
                                @if(method_exists($provider, 'getConfigFields'))
                                    @foreach($provider->getConfigFields() as $field)
                                        <div>
                                            <label for="{{ $field['name'] }}" class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ $field['label'] }}
                                            </label>
                                            @if($field['type'] === 'select')
                                                <select name="{{ $field['name'] }}" 
                                                        id="{{ $field['name'] }}"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
                                                        {{ $field['required'] ? 'required' : '' }}>
                                                    @foreach($field['options'] as $value => $label)
                                                        <option value="{{ $value }}" {{ ($field['default'] ?? '') === $value ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="{{ $field['type'] }}" 
                                                       name="{{ $field['name'] }}" 
                                                       id="{{ $field['name'] }}"
                                                       placeholder="{{ $field['placeholder'] ?? '' }}"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
                                                       {{ $field['required'] ? 'required' : '' }}>
                                            @endif
                                            @if(isset($field['help']))
                                                <p class="mt-1 text-xs text-gray-500">{{ $field['help'] }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                                <div>
                                    <label for="from_email_{{ $key }}" class="block text-sm font-medium text-gray-700 mb-2">
                                        From Email
                                    </label>
                                    <input type="email" name="from_email" id="from_email_{{ $key }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
                                           required>
                                </div>
                                <div>
                                    <label for="from_name_{{ $key }}" class="block text-sm font-medium text-gray-700 mb-2">
                                        From Name
                                    </label>
                                    <input type="text" name="from_name" id="from_name_{{ $key }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
                                           required>
                                </div>
                                <button type="submit" 
                                        class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors text-sm">
                                    Save Configuration
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- Custom SMTP Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Custom
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Custom SMTP</h3>
                <p class="text-sm text-gray-600 mb-6">Configure manually with your own SMTP server details.</p>
                <a href="{{ route('smtp-settings.create') }}"
                   class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Manual Configuration
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
