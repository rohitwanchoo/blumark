<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
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

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Personal Information -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
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
                                       value="{{ old('phone', $user->phone) }}"
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
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
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
                                    <option value="funder" {{ old('company_type', $user->company_type) === 'funder' ? 'selected' : '' }}>Funder</option>
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
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('profile.show') }}" class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium text-sm hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-colors shadow-lg shadow-primary-600/25">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
