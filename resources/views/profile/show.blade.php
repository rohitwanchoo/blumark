<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
            <p class="text-gray-500 mt-1">View and manage your account information</p>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Profile Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Profile Header Card -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-6 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center">
                                <span class="text-3xl font-bold text-primary-600">
                                    {{ strtoupper(substr($user->first_name ?: $user->name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?: '', 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-semibold text-gray-900">{{ $user->getFullName() }}</h2>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                @if($user->company_name)
                                    <p class="text-sm text-gray-400 mt-1">{{ $user->company_name }}</p>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white rounded-xl font-medium text-sm hover:bg-primary-700 transition-colors shadow-lg shadow-primary-600/25">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Profile
                        </a>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                        <p class="text-sm text-gray-500 mt-1">Your personal details and contact information</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500">First Name</p>
                                <p class="text-base font-medium text-gray-900">{{ $user->first_name ?: '-' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500">Last Name</p>
                                <p class="text-base font-medium text-gray-900">{{ $user->last_name ?: '-' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500">Email Address</p>
                                <div class="flex items-center">
                                    <p class="text-base font-medium text-gray-900">{{ $user->email }}</p>
                                    @if($user->email_verified_at)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Verified
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500">Phone Number</p>
                                <p class="text-base font-medium text-gray-900">{{ $user->phone ?: '-' }}</p>
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
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500">Company Name</p>
                                <p class="text-base font-medium text-gray-900">{{ $user->company_name ?: '-' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500">Company Type</p>
                                @if($user->company_type)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $user->company_type === 'iso' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $user->getCompanyTypeLabel() }}
                                    </span>
                                @else
                                    <p class="text-base font-medium text-gray-900">-</p>
                                @endif
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500">Website</p>
                                @if($user->website)
                                    <a href="{{ $user->website }}" target="_blank" class="text-base font-medium text-primary-600 hover:text-primary-700 hover:underline">
                                        {{ $user->website }}
                                    </a>
                                @else
                                    <p class="text-base font-medium text-gray-900">-</p>
                                @endif
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-500">Address</p>
                                <p class="text-base font-medium text-gray-900 whitespace-pre-line">{{ $user->address ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Stats & Quick Actions -->
            <div class="space-y-6">
                <!-- Account Overview -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Account Overview</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Current Plan</p>
                            <p class="text-xl font-bold text-gray-900 mt-1">{{ ucfirst($user->getPlanSlug()) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Credits Available</p>
                            <p class="text-xl font-bold text-gray-900 mt-1">{{ $user->getCredits() }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Jobs This Month</p>
                            <p class="text-xl font-bold text-gray-900 mt-1">{{ $user->getMonthlyJobCount() }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Member Since</p>
                            <p class="text-xl font-bold text-gray-900 mt-1">{{ $user->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-4 space-y-2">
                        <a href="{{ route('profile.edit') }}" class="flex items-center w-full px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-xl transition-colors">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Profile
                        </a>
                        <a href="{{ route('profile.edit') }}#current_password" class="flex items-center w-full px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-xl transition-colors">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Change Password
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
</x-app-layout>
