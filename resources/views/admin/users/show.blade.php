<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
            </div>
            @if($user->id !== Auth::id())
            <div class="flex items-center space-x-3">
                <form action="{{ route('admin.users.impersonate', $user) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Impersonate
                    </button>
                </form>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - User Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- User Details Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">User Details</h2>
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                                <input type="text" name="company_name" value="{{ old('company_name', $user->company_name) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                Update User
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Recent Jobs -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Jobs</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($user->watermarkJobs as $job)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center min-w-0">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-3 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $job->original_filename }}</p>
                                    <p class="text-xs text-gray-500">{{ $job->page_count ?? 0 }} pages</p>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $job->getStatusBadgeClass() }}">
                                    {{ ucfirst($job->status) }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $job->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="px-6 py-8 text-center text-gray-500">
                            No jobs yet.
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Credit Transactions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Credit Transactions</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($user->creditTransactions as $transaction)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $transaction->description }}</p>
                                <p class="text-xs text-gray-500">{{ $transaction->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium {{ $transaction->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->amount >= 0 ? '+' : '' }}{{ $transaction->amount }}
                                </p>
                                <p class="text-xs text-gray-500">Balance: {{ $transaction->balance_after }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="px-6 py-8 text-center text-gray-500">
                            No transactions yet.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column - Actions -->
            <div class="space-y-6">
                <!-- Stats -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Stats</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Role</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'super_admin' ? 'bg-purple-100 text-purple-800' : ($user->role === 'admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $user->getRoleLabel() }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Credits</span>
                            <span class="font-medium text-gray-900">{{ number_format($stats['credits']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Jobs</span>
                            <span class="font-medium text-gray-900">{{ number_format($stats['total_jobs']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Pages</span>
                            <span class="font-medium text-gray-900">{{ number_format($stats['total_pages']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Member Since</span>
                            <span class="font-medium text-gray-900">{{ $user->created_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Add Credits -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Add/Remove Credits</h2>
                    <form action="{{ route('admin.users.credits', $user) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                                <input type="number" name="amount" required
                                       placeholder="Enter amount (negative to remove)"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                <input type="text" name="reason" required
                                       placeholder="Reason for adjustment"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                @error('reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                Apply Credits
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Role (Super Admin Only) -->
                @if(Auth::user()->isSuperAdmin() && $user->id !== Auth::id())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Change Role</h2>
                    <form action="{{ route('admin.users.role', $user) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                Update Role
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                <!-- Delete User -->
                @if($user->id !== Auth::id())
                <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6">
                    <h2 class="text-lg font-semibold text-red-900 mb-4">Danger Zone</h2>
                    <p class="text-sm text-gray-600 mb-4">Permanently delete this user and all their data. This action cannot be undone.</p>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Delete User
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
