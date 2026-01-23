<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-3">
                <a href="{{ route('profile.show') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Profile</h1>
                    <p class="text-gray-500 mt-1">Update your personal and company information</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Profile Information -->
            <div class="lg:col-span-2 space-y-6">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Personal Information -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-5 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                            <p class="text-sm text-gray-500 mt-1">Your personal details and contact information</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                    <input type="text" name="first_name" id="first_name"
                                           value="{{ old('first_name', $user->first_name) }}"
                                           class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-3 border @error('first_name') border-red-500 @enderror"
                                           placeholder="John">
                                    @error('first_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <input type="text" name="last_name" id="last_name"
                                           value="{{ old('last_name', $user->last_name) }}"
                                           class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-3 border @error('last_name') border-red-500 @enderror"
                                           placeholder="Doe">
                                    @error('last_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" name="email" id="email"
                                           value="{{ old('email', $user->email) }}"
                                           required
                                           class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-3 border @error('email') border-red-500 @enderror"
                                           placeholder="john@example.com">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <input type="tel" name="phone" id="phone"
                                           value="{{ old('phone', $user->getFormattedPhone()) }}"
                                           oninput="formatPhoneInput(this)"
                                           class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-3 border @error('phone') border-red-500 @enderror"
                                           placeholder="+1 (555) 123-4567">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Company Information -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-5 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900">Company Information</h3>
                            <p class="text-sm text-gray-500 mt-1">Your business details</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                                    <input type="text" name="company_name" id="company_name"
                                           value="{{ old('company_name', $user->company_name) }}"
                                           class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-3 border @error('company_name') border-red-500 @enderror"
                                           placeholder="Acme Inc.">
                                    @error('company_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="company_type" class="block text-sm font-medium text-gray-700 mb-2">Company Type</label>
                                    <select name="company_type" id="company_type"
                                            class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-3 border @error('company_type') border-red-500 @enderror">
                                        <option value="">Select type...</option>
                                        <option value="iso" {{ old('company_type', $user->company_type) === 'iso' ? 'selected' : '' }}>ISO</option>
                                        <option value="lender" {{ old('company_type', $user->company_type) === 'lender' ? 'selected' : '' }}>Lender</option>
                                    </select>
                                    @error('company_type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="website" class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                                    <input type="url" name="website" id="website"
                                           value="{{ old('website', $user->website) }}"
                                           class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-3 border @error('website') border-red-500 @enderror"
                                           placeholder="https://www.example.com">
                                    @error('website')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                    <textarea name="address" id="address" rows="3"
                                              class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-3 border @error('address') border-red-500 @enderror"
                                              placeholder="123 Main Street, Suite 100&#10;New York, NY 10001">{{ old('address', $user->address) }}</textarea>
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                                    <select name="timezone" id="timezone"
                                            class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-3 border @error('timezone') border-red-500 @enderror">
                                        @php
                                            $timezones = [
                                                'America/New_York' => 'Eastern Time (ET)',
                                                'America/Chicago' => 'Central Time (CT)',
                                                'America/Denver' => 'Mountain Time (MT)',
                                                'America/Phoenix' => 'Arizona (MST - No DST)',
                                                'America/Los_Angeles' => 'Pacific Time (PT)',
                                                'America/Anchorage' => 'Alaska Time (AKT)',
                                                'Pacific/Honolulu' => 'Hawaii Time (HST)',
                                                'UTC' => 'UTC (Coordinated Universal Time)',
                                                'Europe/London' => 'London (GMT/BST)',
                                                'Europe/Paris' => 'Paris (CET/CEST)',
                                                'Asia/Dubai' => 'Dubai (GST)',
                                                'Asia/Kolkata' => 'India (IST)',
                                                'Asia/Singapore' => 'Singapore (SGT)',
                                                'Asia/Tokyo' => 'Tokyo (JST)',
                                                'Australia/Sydney' => 'Sydney (AEDT/AEST)',
                                            ];
                                        @endphp
                                        @foreach($timezones as $tz => $label)
                                            @php
                                                $datetime = new DateTime('now', new DateTimeZone($tz));
                                                $offset = $datetime->format('P');
                                            @endphp
                                            <option value="{{ $tz }}" {{ old('timezone', $user->timezone ?? 'UTC') === $tz ? 'selected' : '' }}>
                                                {{ $label }} (UTC{{ $offset }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('timezone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Save Profile Button -->
                    <div class="flex items-center justify-end">
                        <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-colors shadow-lg shadow-primary-600/25">
                            Save Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Column: Password & Actions -->
            <div class="space-y-6">
                <!-- Change Password -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Change Password</h3>
                        <p class="text-sm text-gray-500 mt-1">Update your account password</p>
                    </div>
                    <form action="{{ route('profile.password') }}" method="POST" class="p-6">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                <input type="password" name="current_password" id="current_password"
                                       required
                                       class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-3 border @error('current_password') border-red-500 @enderror"
                                       placeholder="Enter current password">
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                <input type="password" name="password" id="password"
                                       required
                                       class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-3 border @error('password') border-red-500 @enderror"
                                       placeholder="Enter new password">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Min 8 characters, mixed case and numbers</p>
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       required
                                       class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-3 border"
                                       placeholder="Confirm new password">
                            </div>

                            <button type="submit" class="w-full px-4 py-3 bg-gray-900 text-white rounded-xl font-semibold text-sm hover:bg-gray-800 transition-colors">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-4 space-y-2">
                        <a href="{{ route('profile.show') }}" class="flex items-center w-full px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-xl transition-colors">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            View Profile
                        </a>
                        <a href="{{ route('billing.index') }}" class="flex items-center w-full px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-xl transition-colors">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Manage Billing
                        </a>
                        <a href="{{ route('jobs.index') }}" class="flex items-center w-full px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-xl transition-colors">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            View Jobs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function formatPhoneInput(input) {
            // Get cursor position
            let cursorPos = input.selectionStart;
            let oldLength = input.value.length;

            // Remove all non-numeric characters
            let digits = input.value.replace(/\D/g, '');

            // Remove leading 1 if present (we'll add it back formatted)
            if (digits.startsWith('1') && digits.length > 10) {
                digits = digits.substring(1);
            }

            // Limit to 10 digits (excluding country code)
            digits = digits.substring(0, 10);

            // Format the number
            let formatted = '';
            if (digits.length === 0) {
                formatted = '';
            } else if (digits.length <= 3) {
                formatted = '+1 (' + digits;
            } else if (digits.length <= 6) {
                formatted = '+1 (' + digits.substring(0, 3) + ') ' + digits.substring(3);
            } else {
                formatted = '+1 (' + digits.substring(0, 3) + ') ' + digits.substring(3, 6) + '-' + digits.substring(6);
            }

            // Update value
            input.value = formatted;

            // Adjust cursor position
            let newLength = formatted.length;
            let newCursorPos = cursorPos + (newLength - oldLength);
            if (newCursorPos < 0) newCursorPos = 0;
            if (newCursorPos > newLength) newCursorPos = newLength;

            // Set cursor position after a brief delay
            setTimeout(() => {
                input.setSelectionRange(newCursorPos, newCursorPos);
            }, 0);
        }
    </script>
</x-app-layout>
