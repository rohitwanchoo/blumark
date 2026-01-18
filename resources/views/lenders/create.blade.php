<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <a href="{{ route('lenders.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Lenders
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Add New Lender</h1>
            <p class="text-gray-500 mt-1">Create a new lender contact for document distribution</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <form action="{{ route('lenders.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Company Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Company Information</h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Company Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="company_name"
                                   id="company_name"
                                   value="{{ old('company_name') }}"
                                   required
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 focus:border-primary-500 focus:ring-primary-500 focus:outline-none @error('company_name') border-red-300 @enderror">
                            @error('company_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Person -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Person (Optional)</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text"
                                   name="first_name"
                                   id="first_name"
                                   value="{{ old('first_name') }}"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text"
                                   name="last_name"
                                   id="last_name"
                                   value="{{ old('last_name') }}"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Email Addresses -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Email Addresses</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Primary Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   value="{{ old('email') }}"
                                   required
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 focus:border-primary-500 focus:ring-primary-500 focus:outline-none @error('email') border-red-300 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email_2" class="block text-sm font-medium text-gray-700 mb-1">Secondary Email</label>
                            <input type="email"
                                   name="email_2"
                                   id="email_2"
                                   value="{{ old('email_2') }}"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 focus:border-primary-500 focus:ring-primary-500 focus:outline-none @error('email_2') border-red-300 @enderror">
                            @error('email_2')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email_3" class="block text-sm font-medium text-gray-700 mb-1">Additional Email</label>
                            <input type="email"
                                   name="email_3"
                                   id="email_3"
                                   value="{{ old('email_3') }}"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 focus:border-primary-500 focus:ring-primary-500 focus:outline-none @error('email_3') border-red-300 @enderror">
                            @error('email_3')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information (Optional)</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text"
                                   name="phone"
                                   id="phone"
                                   value="{{ old('phone') }}"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea name="address"
                                      id="address"
                                      rows="2"
                                      class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 focus:border-primary-500 focus:ring-primary-500 focus:outline-none">{{ old('address') }}</textarea>
                        </div>
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea name="notes"
                                      id="notes"
                                      rows="3"
                                      class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 focus:border-primary-500 focus:ring-primary-500 focus:outline-none">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="border-t border-gray-200 pt-6 flex justify-end gap-4">
                    <a href="{{ route('lenders.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900 font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        Create Lender
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
