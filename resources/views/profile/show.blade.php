<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
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

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <!-- Profile Header -->
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl font-bold text-primary-600">
                            {{ strtoupper(substr($user->first_name ?: $user->name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?: '', 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $user->getFullName() }}</h2>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-xl font-medium text-sm hover:bg-primary-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Profile
                </a>
            </div>

            <!-- Profile Details -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Personal Information</h3>

                        <div class="space-y-3">
                            <div class="flex items-start">
                                <div class="w-32 flex-shrink-0">
                                    <span class="text-sm text-gray-500">First Name</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $user->first_name ?: '-' }}</span>
                            </div>

                            <div class="flex items-start">
                                <div class="w-32 flex-shrink-0">
                                    <span class="text-sm text-gray-500">Last Name</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $user->last_name ?: '-' }}</span>
                            </div>

                            <div class="flex items-start">
                                <div class="w-32 flex-shrink-0">
                                    <span class="text-sm text-gray-500">Email</span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-900">{{ $user->email }}</span>
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

                            <div class="flex items-start">
                                <div class="w-32 flex-shrink-0">
                                    <span class="text-sm text-gray-500">Phone</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $user->phone ?: '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Company Information -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Company Information</h3>

                        <div class="space-y-3">
                            <div class="flex items-start">
                                <div class="w-32 flex-shrink-0">
                                    <span class="text-sm text-gray-500">Company</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $user->company_name ?: '-' }}</span>
                            </div>

                            <div class="flex items-start">
                                <div class="w-32 flex-shrink-0">
                                    <span class="text-sm text-gray-500">Type</span>
                                </div>
                                @if($user->company_type)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->company_type === 'iso' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $user->getCompanyTypeLabel() }}
                                    </span>
                                @else
                                    <span class="text-sm font-medium text-gray-900">-</span>
                                @endif
                            </div>

                            <div class="flex items-start">
                                <div class="w-32 flex-shrink-0">
                                    <span class="text-sm text-gray-500">Website</span>
                                </div>
                                @if($user->website)
                                    <a href="{{ $user->website }}" target="_blank" class="text-sm font-medium text-primary-600 hover:text-primary-700">
                                        {{ $user->website }}
                                    </a>
                                @else
                                    <span class="text-sm font-medium text-gray-900">-</span>
                                @endif
                            </div>

                            <div class="flex items-start">
                                <div class="w-32 flex-shrink-0">
                                    <span class="text-sm text-gray-500">Address</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $user->address ?: '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Stats -->
            <div class="px-6 py-5 bg-gray-50 border-t border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Account Overview</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl p-4 border border-gray-100">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Plan</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ ucfirst($user->getPlanSlug()) }}</p>
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-gray-100">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Credits</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $user->getCredits() }}</p>
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-gray-100">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Jobs This Month</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $user->getMonthlyJobCount() }}</p>
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-gray-100">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Member Since</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $user->created_at->format('M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
