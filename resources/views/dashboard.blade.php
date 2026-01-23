<x-app-layout>
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8" x-data="watermarkForm()">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-500 mt-1">Welcome back! Upload PDFs to add watermarks.</p>
        </div>

        <!-- Billing & Usage Widget -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">
            <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <!-- Plan Info -->
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center
                            @if($billing['plan_slug'] === 'enterprise') bg-purple-100
                            @elseif($billing['plan_slug'] === 'pro') bg-primary-100
                            @else bg-gray-100 @endif">
                            @if($billing['plan_slug'] === 'enterprise')
                                <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            @elseif($billing['plan_slug'] === 'pro')
                                <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            @else
                                <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h3 class="text-lg font-bold text-gray-900">{{ $billing['plan_name'] }} Plan</h3>
                                @if($billing['on_grace_period'])
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Canceling
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 mt-0.5">
                                @if($billing['jobs_limit'] === null)
                                    Unlimited submissions per month
                                @else
                                    {{ $billing['jobs_limit'] }} submissions per month
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Usage Stats -->
                    <div class="flex-1 lg:max-w-md">
                        @if($billing['jobs_limit'] !== null)
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Monthly Usage</span>
                                <span class="text-sm font-semibold
                                    @if($billing['usage_percentage'] >= 90) text-red-600
                                    @elseif($billing['usage_percentage'] >= 75) text-yellow-600
                                    @else text-gray-900 @endif">
                                    {{ $billing['jobs_used'] }} / {{ $billing['jobs_limit'] }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="h-3 rounded-full transition-all duration-500
                                    @if($billing['usage_percentage'] >= 90) bg-red-500
                                    @elseif($billing['usage_percentage'] >= 75) bg-yellow-500
                                    @else bg-primary-500 @endif"
                                    style="width: {{ $billing['usage_percentage'] }}%"></div>
                            </div>
                            <p class="text-xs mt-2">
                                @if($billing['total_available'] > 0)
                                    <span class="text-green-600 font-medium">{{ $billing['total_available'] }} watermark jobs available</span>
                                    @if($billing['credits'] > 0)
                                        <span class="text-gray-400">({{ $billing['jobs_remaining'] ?? 0 }} plan + {{ $billing['credits'] }} credits)</span>
                                    @endif
                                @else
                                    <span class="text-red-600 font-medium">No watermark jobs available. <a href="{{ route('billing.credits') }}" class="underline">Buy credits</a></span>
                                @endif
                            </p>
                        @else
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Monthly Usage</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $billing['jobs_used'] }} used</span>
                            </div>
                            <div class="flex items-center space-x-2 text-green-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-medium">Unlimited watermark jobs</span>
                            </div>
                        @endif
                    </div>

                    <!-- Credits & Actions -->
                    <div class="flex items-center space-x-4">
                        @if($billing['credits'] > 0)
                            <div class="text-center px-4 py-2 bg-green-50 rounded-xl border border-green-100">
                                <p class="text-2xl font-bold text-green-600">{{ $billing['credits'] }}</p>
                                <p class="text-xs text-green-700">Credits</p>
                            </div>
                        @endif

                        <div class="flex flex-col space-y-2">
                            <a href="{{ route('billing.plans') }}" class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                    {{ in_array($billing['plan_slug'], ['free', 'pro']) ? 'Upgrade Plan' : 'Manage Plan' }}
                                </a>
                            <a href="{{ route('billing.credits') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Buy Credits
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Documents Processed</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_jobs'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Completed</p>
                        <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['completed_jobs'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pending</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['pending_jobs'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Failed</p>
                        <p class="text-3xl font-bold text-red-600 mt-1">{{ $stats['failed_jobs'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
            <!-- Upload Form -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Add Watermark to PDFs</h3>
                    <p class="text-sm text-gray-500 mt-1">Upload multiple documents and configure your watermark settings</p>
                </div>
                <div class="p-6">
                    <!-- Drag and Drop Zone -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">PDF Files</label>
                        <div class="relative"
                             @dragover.prevent="isDragging = true"
                             @dragleave.prevent="isDragging = false"
                             @drop.prevent="handleDrop($event)">
                            <div class="border-2 border-dashed rounded-xl p-8 text-center transition-all duration-200 cursor-pointer"
                                 :class="isDragging ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-primary-300 hover:bg-gray-50'"
                                 @click="$refs.fileInput.click()">
                                <input type="file"
                                       x-ref="fileInput"
                                       accept=".pdf"
                                       multiple
                                       class="hidden"
                                       @change="handleFileSelect($event)">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700">Drop PDF files here or click to browse</p>
                                    <p class="text-xs text-gray-500 mt-1">Maximum {{ config('watermark.max_upload_mb') }}MB per file</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Queue -->
                    <div x-show="files.length > 0" x-cloak class="mb-6">
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-sm font-medium text-gray-700">
                                Selected Files (<span x-text="files.length"></span>)
                            </label>
                            <button type="button"
                                    @click="clearFiles()"
                                    class="text-xs text-red-600 hover:text-red-700 font-medium"
                                    x-show="!isUploading">
                                Clear all
                            </button>
                        </div>
                        <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                            <template x-for="(file, index) in files" :key="file.id">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="flex items-center space-x-3 min-w-0 flex-1">
                                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 truncate" x-text="file.name"></p>
                                            <p class="text-xs text-gray-500" x-text="formatFileSize(file.size)"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 ml-3">
                                        <!-- Status indicators -->
                                        <template x-if="file.status === 'pending'">
                                            <span class="text-xs text-gray-500">Waiting</span>
                                        </template>
                                        <template x-if="file.status === 'uploading'">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                                    <div class="bg-primary-600 h-1.5 rounded-full transition-all duration-300"
                                                         :style="'width: ' + file.progress + '%'"></div>
                                                </div>
                                                <span class="text-xs text-primary-600 font-medium" x-text="file.progress + '%'"></span>
                                            </div>
                                        </template>
                                        <template x-if="file.status === 'done'">
                                            <div class="flex items-center space-x-1 text-green-600">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="text-xs font-medium">Done</span>
                                            </div>
                                        </template>
                                        <template x-if="file.status === 'error'">
                                            <div class="flex items-center space-x-1 text-red-600">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="text-xs font-medium">Failed</span>
                                            </div>
                                        </template>
                                        <!-- Remove button (only when not uploading) -->
                                        <button type="button"
                                                @click="removeFile(index)"
                                                x-show="file.status === 'pending' || file.status === 'error'"
                                                class="p-1 text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- ISO Name Warning -->
                    @if(empty(Auth::user()->company_name))
                    <div class="mb-6 p-4 bg-orange-50 rounded-xl border border-orange-200">
                        <div class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-orange-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-orange-800">ISO Name Required</p>
                                <p class="text-sm text-orange-700 mt-1">Please set your Company Name in your profile to use as your ISO name on watermarks.</p>
                                <a href="{{ route('profile.edit') }}" class="inline-flex items-center mt-2 text-sm font-medium text-orange-800 hover:text-orange-900">
                                    Update Profile
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Watermark Text -->
                    <div class="space-y-4 mb-6">
                        <!-- ISO Name -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="iso" class="block text-sm font-medium text-gray-700">ISO Name (from profile)</label>
                                <a href="{{ route('profile.edit') }}" class="text-xs text-primary-600 hover:text-primary-700 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                    Edit
                                </a>
                            </div>
                            <input type="text" id="iso" x-model="iso" required maxlength="50" readonly
                                   class="block w-full rounded-xl border-gray-200 bg-gray-50 text-gray-600 cursor-not-allowed text-sm px-4 py-3 border"
                                   placeholder="Set in your profile">
                        </div>

                        <!-- Lender Name -->
                        <div>
                            <label for="lender" class="block text-sm font-medium text-gray-700 mb-2">Lender Name</label>
                            <input type="text" id="lender" x-model="lender" required maxlength="50"
                                   class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-3 border"
                                   placeholder="Enter Lender Name">
                        </div>
                    </div>

                    <!-- Template Selection -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <label for="template" class="block text-sm font-medium text-gray-700">Quick Template</label>
                            <button type="button"
                                    @click="saveAsTemplate()"
                                    :disabled="!iso.trim() || !lender.trim()"
                                    class="text-xs text-primary-600 hover:text-primary-700 font-medium disabled:text-gray-400 disabled:cursor-not-allowed">
                                + Save as Template
                            </button>
                        </div>
                        <select x-model="selectedTemplate"
                                @change="applyTemplate()"
                                class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-3 border">
                            <option value="">-- None (Custom Settings) --</option>
                            <template x-for="template in templates" :key="template.id">
                                <option :value="template.id" x-text="template.name"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Appearance Settings -->
                    <div class="space-y-4 mb-6">
                        <!-- Color -->
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Watermark Color</label>
                            <div class="flex items-center space-x-3">
                                <input type="color" id="color" x-model="color"
                                       class="h-10 w-16 rounded-lg cursor-pointer border border-gray-200">
                                <span class="text-sm text-gray-600 font-mono bg-gray-50 px-3 py-2 rounded-lg border border-gray-200" x-text="color"></span>
                            </div>
                        </div>

                        <!-- Position -->
                        <div x-show="!selectedTemplate">
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                            <select id="position" x-model="position" @change="updateRotationForPosition()"
                                    class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-3 border">
                                <option value="diagonal">Diagonal (Center)</option>
                                <option value="scattered">Scattered (Multiple)</option>
                                <option value="top-left">Top Left</option>
                                <option value="top-center">Top Center</option>
                                <option value="top-right">Top Right</option>
                                <option value="center">Center</option>
                                <option value="bottom-left">Bottom Left</option>
                                <option value="bottom-center">Bottom Center</option>
                                <option value="bottom-right">Bottom Right</option>
                            </select>
                        </div>

                        <!-- Template info when selected -->
                        <div x-show="selectedTemplate" x-cloak class="p-3 bg-primary-50 rounded-lg border border-primary-200">
                            <div class="flex items-start space-x-2">
                                <svg class="w-5 h-5 text-primary-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="text-xs text-primary-700">
                                    <p class="font-medium">Template settings active</p>
                                    <p class="mt-1">Position is controlled by the selected template.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Progress Summary -->
                    <div x-show="isUploading" x-cloak class="mb-4 p-4 bg-primary-50 rounded-xl border border-primary-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-primary-700">Uploading files...</span>
                            <span class="text-sm text-primary-600" x-text="uploadedCount + ' / ' + files.length"></span>
                        </div>
                        <div class="w-full bg-primary-200 rounded-full h-2">
                            <div class="bg-primary-600 h-2 rounded-full transition-all duration-300"
                                 :style="'width: ' + overallProgress + '%'"></div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div x-show="errorMessage" x-cloak class="mb-4 p-4 bg-red-50 rounded-xl border border-red-100">
                        <div class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-red-700" x-text="errorMessage"></p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="button"
                            @click="startUpload()"
                            :disabled="!canUpload || !iso.trim()"
                            :class="(canUpload && iso.trim()) ? 'bg-primary-600 hover:bg-primary-700 focus:bg-primary-700 shadow-lg shadow-primary-600/25' : 'bg-gray-300 cursor-not-allowed'"
                            class="w-full inline-flex justify-center items-center px-6 py-3.5 border border-transparent rounded-xl font-semibold text-sm text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200">
                        <template x-if="!isUploading">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span x-text="files.length > 1 ? 'Upload & Watermark ' + files.length + ' Files' : 'Upload & Add Watermark'"></span>
                            </span>
                        </template>
                        <template x-if="isUploading">
                            <span class="flex items-center">
                                <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Uploading...
                            </span>
                        </template>
                    </button>

                    <!-- Success Message -->
                    <div x-show="successMessage" x-cloak class="mt-4 p-4 bg-green-50 rounded-xl border border-green-100">
                        <div class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-green-700" x-text="successMessage"></p>
                                <a href="{{ route('jobs.index') }}" class="text-sm text-green-600 hover:text-green-700 underline mt-1 inline-block">View all jobs</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Real-time Watermark Preview -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Live Preview</h3>
                            <p class="text-sm text-gray-500 mt-1">See how your watermark will appear</p>
                        </div>
                        <div class="flex items-center space-x-1" x-show="previewReady">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            <span class="text-xs text-gray-500">Real-time</span>
                        </div>
                    </div>
                </div>
                <div class="p-6 overflow-hidden">
                    <!-- Preview Document -->
                    <div class="relative bg-white border-2 border-gray-200 rounded-lg shadow-inner mx-auto overflow-hidden" style="aspect-ratio: 8.5/11; max-width: 340px;">
                        <!-- Placeholder when no PDF selected - shows sample document with watermark -->
                        <div x-show="!previewReady && !previewLoading" class="absolute inset-0" x-ref="previewContainer">
                            <!-- Simulated document content -->
                            <div class="absolute inset-0 p-6 overflow-hidden pointer-events-none">
                                <div class="space-y-2 mt-4">
                                    <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                                    <div class="h-2 bg-gray-100 rounded w-full"></div>
                                    <div class="h-2 bg-gray-100 rounded w-full"></div>
                                    <div class="h-2 bg-gray-100 rounded w-5/6"></div>
                                    <div class="h-2 bg-gray-100 rounded w-full"></div>
                                    <div class="h-2 bg-gray-100 rounded w-4/5"></div>
                                    <div class="h-3 bg-gray-200 rounded w-1/2 mt-4"></div>
                                    <div class="h-2 bg-gray-100 rounded w-full"></div>
                                    <div class="h-2 bg-gray-100 rounded w-full"></div>
                                    <div class="h-2 bg-gray-100 rounded w-3/4"></div>
                                </div>
                            </div>
                            <!-- Watermark overlay -->
                            <div class="absolute inset-0 pointer-events-none overflow-visible">
                                <!-- Single watermark (non-scattered) -->
                                <template x-if="position !== 'scattered'">
                                    <div class="absolute watermark-text font-bold whitespace-nowrap"
                                         style="font-family: Helvetica, Arial, sans-serif;"
                                         :style="{
                                             ...getPreviewPositionAbsolute(position),
                                             transform: getPreviewTransform(position, rotation),
                                             color: color,
                                             opacity: opacity / 100,
                                             fontSize: calculateWatermarkFontSize() + 'px',
                                             transformOrigin: 'center'
                                         }"
                                         x-text="getFullWatermarkText()">
                                    </div>
                                </template>

                                <!-- Scattered watermarks -->
                                <template x-if="position === 'scattered'">
                                    <div>
                                        <div class="absolute watermark-text font-bold whitespace-nowrap" style="font-family: Helvetica, Arial, sans-serif; left: 33.33%; top: 10%;"
                                             :style="{ transform: `translate(-50%, -50%) rotate(${rotation}deg)`, color: color, opacity: opacity / 100, fontSize: (calculateWatermarkFontSize() * 0.35) + 'px', transformOrigin: 'center' }"
                                             x-text="getFullWatermarkText()"></div>
                                        <div class="absolute watermark-text font-bold whitespace-nowrap" style="font-family: Helvetica, Arial, sans-serif; left: 66.67%; top: 10%;"
                                             :style="{ transform: `translate(-50%, -50%) rotate(${rotation}deg)`, color: color, opacity: opacity / 100, fontSize: (calculateWatermarkFontSize() * 0.35) + 'px', transformOrigin: 'center' }"
                                             x-text="getFullWatermarkText()"></div>
                                        <div class="absolute watermark-text font-bold whitespace-nowrap" style="font-family: Helvetica, Arial, sans-serif; left: 20%; top: 50%;"
                                             :style="{ transform: `translate(-50%, -50%) rotate(${rotation}deg)`, color: color, opacity: opacity / 100, fontSize: (calculateWatermarkFontSize() * 0.35) + 'px', transformOrigin: 'center' }"
                                             x-text="getFullWatermarkText()"></div>
                                        <div class="absolute watermark-text font-bold whitespace-nowrap" style="font-family: Helvetica, Arial, sans-serif; left: 50%; top: 50%;"
                                             :style="{ transform: `translate(-50%, -50%) rotate(${rotation}deg)`, color: color, opacity: opacity / 100, fontSize: (calculateWatermarkFontSize() * 0.35) + 'px', transformOrigin: 'center' }"
                                             x-text="getFullWatermarkText()"></div>
                                        <div class="absolute watermark-text font-bold whitespace-nowrap" style="font-family: Helvetica, Arial, sans-serif; left: 80%; top: 50%;"
                                             :style="{ transform: `translate(-50%, -50%) rotate(${rotation}deg)`, color: color, opacity: opacity / 100, fontSize: (calculateWatermarkFontSize() * 0.35) + 'px', transformOrigin: 'center' }"
                                             x-text="getFullWatermarkText()"></div>
                                        <div class="absolute watermark-text font-bold whitespace-nowrap" style="font-family: Helvetica, Arial, sans-serif; left: 20%; top: 90%;"
                                             :style="{ transform: `translate(-50%, -50%) rotate(${rotation}deg)`, color: color, opacity: opacity / 100, fontSize: (calculateWatermarkFontSize() * 0.35) + 'px', transformOrigin: 'center' }"
                                             x-text="getFullWatermarkText()"></div>
                                        <div class="absolute watermark-text font-bold whitespace-nowrap" style="font-family: Helvetica, Arial, sans-serif; left: 50%; top: 90%;"
                                             :style="{ transform: `translate(-50%, -50%) rotate(${rotation}deg)`, color: color, opacity: opacity / 100, fontSize: (calculateWatermarkFontSize() * 0.35) + 'px', transformOrigin: 'center' }"
                                             x-text="getFullWatermarkText()"></div>
                                        <div class="absolute watermark-text font-bold whitespace-nowrap" style="font-family: Helvetica, Arial, sans-serif; left: 80%; top: 90%;"
                                             :style="{ transform: `translate(-50%, -50%) rotate(${rotation}deg)`, color: color, opacity: opacity / 100, fontSize: (calculateWatermarkFontSize() * 0.35) + 'px', transformOrigin: 'center' }"
                                             x-text="getFullWatermarkText()"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Loading indicator -->
                        <div x-show="previewLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90 z-10">
                            <div class="flex flex-col items-center">
                                <svg class="animate-spin w-8 h-8 text-primary-600 mb-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-xs text-gray-500">Loading preview...</span>
                            </div>
                        </div>

                        <!-- PDF Canvas -->
                        <canvas x-ref="pdfCanvas" x-show="previewReady" class="w-full h-full object-contain"></canvas>

                        <!-- Watermark overlay on PDF -->
                        <div x-show="previewReady" class="absolute inset-0 pointer-events-none overflow-visible">
                            <!-- Single watermark (non-scattered) -->
                            <template x-if="position !== 'scattered'">
                                <div class="absolute watermark-text font-bold whitespace-nowrap"
                                     style="font-family: Helvetica, Arial, sans-serif;"
                                     :style="{
                                         ...getPreviewPositionAbsolute(position),
                                         transform: getPreviewTransform(position, rotation),
                                         color: color,
                                         opacity: opacity / 100,
                                         fontSize: calculateWatermarkFontSize() + 'px',
                                         transformOrigin: 'center'
                                     }"
                                     x-text="getFullWatermarkText()">
                                </div>
                            </template>

                            <!-- Scattered watermarks -->
                            <template x-if="position === 'scattered'">
                                <div>
                                    <div class="absolute watermark-text font-bold whitespace-nowrap" style="font-family: Helvetica, Arial, sans-serif; left: 33.33%; top: 10%;"
                                         :style="{ transform: `translate(-50%, -50%) rotate(${rotation}deg)`, color: color, opacity: opacity / 100, fontSize: (calculateWatermarkFontSize() * 0.35) + 'px', transformOrigin: 'center' }"
                                         x-text="getFullWatermarkText()"></div>
                                    <div class="absolute watermark-text font-bold whitespace-nowrap" style="font-family: Helvetica, Arial, sans-serif; left: 66.67%; top: 10%;"
                                         :style="{ transform: `translate(-50%, -50%) rotate(${rotation}deg)`, color: color, opacity: opacity / 100, fontSize: (calculateWatermarkFontSize() * 0.35) + 'px', transformOrigin: 'center' }"
                                         x-text="getFullWatermarkText()"></div>
                                    <div class="absolute watermark-text font-bold whitespace-nowrap" style="font-family: Helvetica, Arial, sans-serif; left: 20%; top: 50%;"
                                         :style="{ transform: `translate(-50%, -50%) rotate(${rotation}deg)`, color: color, opacity: opacity / 100, fontSize: (calculateWatermarkFontSize() * 0.35) + 'px', transformOrigin: 'center' }"
                                         x-text="getFullWatermarkText()"></div>
                                    <div class="absolute watermark-text font-bold whitespace-nowrap" style="font-family: Helvetica, Arial, sans-serif; left: 50%; top: 50%;"
                                         :style="{ transform: `translate(-50%, -50%) rotate(${rotation}deg)`, color: color, opacity: opacity / 100, fontSize: (calculateWatermarkFontSize() * 0.35) + 'px', transformOrigin: 'center' }"
                                         x-text="getFullWatermarkText()"></div>
                                    <div class="absolute watermark-text font-bold whitespace-nowrap" style="font-family: Helvetica, Arial, sans-serif; left: 80%; top: 50%;"
                                         :style="{ transform: `translate(-50%, -50%) rotate(${rotation}deg)`, color: color, opacity: opacity / 100, fontSize: (calculateWatermarkFontSize() * 0.35) + 'px', transformOrigin: 'center' }"
                                         x-text="getFullWatermarkText()"></div>
                                    <div class="absolute watermark-text font-bold whitespace-nowrap" style="font-family: Helvetica, Arial, sans-serif; left: 20%; top: 90%;"
                                         :style="{ transform: `translate(-50%, -50%) rotate(${rotation}deg)`, color: color, opacity: opacity / 100, fontSize: (calculateWatermarkFontSize() * 0.35) + 'px', transformOrigin: 'center' }"
                                         x-text="getFullWatermarkText()"></div>
                                    <div class="absolute watermark-text font-bold whitespace-nowrap" style="font-family: Helvetica, Arial, sans-serif; left: 50%; top: 90%;"
                                         :style="{ transform: `translate(-50%, -50%) rotate(${rotation}deg)`, color: color, opacity: opacity / 100, fontSize: (calculateWatermarkFontSize() * 0.35) + 'px', transformOrigin: 'center' }"
                                         x-text="getFullWatermarkText()"></div>
                                    <div class="absolute watermark-text font-bold whitespace-nowrap" style="font-family: Helvetica, Arial, sans-serif; left: 80%; top: 90%;"
                                         :style="{ transform: `translate(-50%, -50%) rotate(${rotation}deg)`, color: color, opacity: opacity / 100, fontSize: (calculateWatermarkFontSize() * 0.35) + 'px', transformOrigin: 'center' }"
                                         x-text="getFullWatermarkText()"></div>
                                </div>
                            </template>
                        </div>

                        <!-- Corner fold effect -->
                        <div class="absolute top-0 right-0 w-6 h-6 bg-gradient-to-bl from-gray-200 to-transparent"></div>
                    </div>

                    <!-- Preview Info -->
                    <div class="mt-4 p-3 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Position:</span>
                            <span class="font-semibold text-gray-700 capitalize" x-text="position.replace('-', ' ')"></span>
                        </div>
                        <div class="flex items-center justify-between text-xs mt-1">
                            <span class="text-gray-500">Text:</span>
                            <span class="font-mono text-gray-700 truncate max-w-[180px]" x-text="getFullWatermarkText()"></span>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="mt-4 flex items-start space-x-2 text-xs text-gray-500">
                        <svg class="w-4 h-4 text-primary-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Single diagonal watermark placed across the center of each page.</span>
                    </div>
                </div>
            </div>

            <!-- Recent Submissions -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Recent Submissions</h3>
                        <p class="text-sm text-gray-500 mt-1">Documents sent to lenders</p>
                    </div>
                    <a href="{{ route('distributions.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium flex items-center">
                        View all
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <div class="p-6">
                    @if($recentDistributions->isEmpty())
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900 mb-1">No submissions yet</h4>
                            <p class="text-sm text-gray-500 mb-4">Send watermarked documents to your lenders</p>
                            <a href="{{ route('distributions.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                New Submission
                            </a>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($recentDistributions as $distribution)
                                <a href="{{ route('distributions.show', $distribution) }}" class="block p-4 rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50/50 transition-all duration-200 group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3 min-w-0">
                                            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-primary-200 transition-colors">
                                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                </svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $distribution->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $distribution->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            @if($distribution->status === 'completed')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Done
                                                </span>
                                            @elseif($distribution->status === 'processing' || $distribution->status === 'pending')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                    <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                    </svg>
                                                    Processing
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                    Failed
                                                </span>
                                            @endif
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex items-center mt-2 text-xs text-gray-500 ml-[52px]">
                                        <span class="flex items-center mr-3">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            {{ $distribution->items->unique('lender_id')->count() }} {{ Str::plural('lender', $distribution->items->unique('lender_id')->count()) }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            {{ $distribution->items->count() }} {{ Str::plural('file', $distribution->items->count()) }}
                                        </span>
                                        @php
                                            $sentCount = $distribution->items->filter(fn($i) => $i->isSent())->count();
                                        @endphp
                                        @if($sentCount > 0)
                                            <span class="ml-auto text-green-600 font-medium">
                                                {{ $sentCount }}/{{ $distribution->items->count() }} sent
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- PDF.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        // Set PDF.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        function watermarkForm() {
            const defaults = @json($defaults ?? []);
            const maxFileSize = {{ config('watermark.max_upload_mb', 50) }} * 1024 * 1024;
            const csrfToken = '{{ csrf_token() }}';
            const userCompanyName = @json($user->company_name ?? '');

            return {
                // Form fields
                iso: userCompanyName,
                lender: '',
                fontSize: defaults.font_size || 15,
                color: defaults.color || '#878787',
                opacity: defaults.opacity || 10,
                position: defaults.position || 'diagonal',
                rotation: ((defaults.position || 'diagonal') === 'diagonal' || (defaults.position || 'diagonal') === 'scattered') ? -45 : 0,

                // Templates
                templates: [],
                selectedTemplate: '',

                // File handling
                files: [],
                isDragging: false,
                isUploading: false,
                uploadedCount: 0,
                errorMessage: '',
                successMessage: '',
                fileIdCounter: 0,

                // PDF Preview
                previewReady: false,
                previewLoading: false,
                currentPreviewFile: null,

                init() {
                    this.loadTemplates();
                },

                async loadTemplates() {
                    try {
                        const response = await fetch('{{ route('templates.list') }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (response.ok) {
                            this.templates = await response.json();
                        }
                    } catch (e) {
                        console.error('Failed to load templates:', e);
                    }
                },

                applyTemplate() {
                    if (!this.selectedTemplate) return;
                    const template = this.templates.find(t => t.id == this.selectedTemplate);
                    if (template) {
                        this.iso = template.iso;
                        this.lender = template.lender;
                        this.fontSize = template.font_size;
                        this.color = template.color;
                        this.opacity = template.opacity;
                        this.position = template.position || 'diagonal';
                        this.rotation = template.rotation || ((template.position === 'diagonal' || template.position === 'scattered') ? -45 : 0);
                    }
                },

                updateRotationForPosition() {
                    // Automatically set rotation based on position
                    if (this.position === 'diagonal' || this.position === 'scattered') {
                        this.rotation = -45;
                    } else {
                        this.rotation = 0;
                    }
                },

                async saveAsTemplate() {
                    if (!this.iso.trim() || !this.lender.trim()) return;

                    const name = prompt('Enter a name for this template:', `${this.iso} - ${this.lender}`);
                    if (!name) return;

                    try {
                        const response = await fetch('{{ route('templates.quick') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                name: name,
                                iso: this.iso,
                                lender: this.lender,
                                font_size: this.fontSize,
                                color: this.color,
                                opacity: this.opacity,
                                position: this.position,
                                rotation: this.rotation
                            })
                        });

                        if (response.ok) {
                            await this.loadTemplates();
                            alert('Template saved successfully!');
                        } else {
                            const data = await response.json();
                            alert(data.message || 'Failed to save template');
                        }
                    } catch (e) {
                        console.error('Failed to save template:', e);
                        alert('Failed to save template');
                    }
                },

                get canUpload() {
                    return this.files.length > 0 &&
                           this.iso.trim() !== '' &&
                           this.lender.trim() !== '' &&
                           !this.isUploading &&
                           this.files.some(f => f.status === 'pending');
                },

                get overallProgress() {
                    if (this.files.length === 0) return 0;
                    const totalProgress = this.files.reduce((sum, f) => {
                        if (f.status === 'done') return sum + 100;
                        if (f.status === 'uploading') return sum + f.progress;
                        return sum;
                    }, 0);
                    return Math.round(totalProgress / this.files.length);
                },

                getWatermarkText() {
                    const isoText = this.iso || 'ISO Name';
                    const lenderText = this.lender || 'Lender Name';
                    return `${isoText} | ${lenderText}`;
                },

                getFullWatermarkText() {
                    const isoText = this.iso || 'ISO Name';
                    const lenderText = this.lender || 'Lender Name';
                    return `ISO: ${isoText} | Lender: ${lenderText}`;
                },

                calculateWatermarkFontSize() {
                    // Preview container is 340px wide with aspect ratio 8.5/11
                    const containerWidth = 340;
                    const containerHeight = containerWidth * (11 / 8.5); // ~440px

                    // Calculate diagonal (like the actual watermark does)
                    const diagonal = Math.sqrt(containerWidth * containerWidth + containerHeight * containerHeight);

                    // Target: text should span 80% of diagonal
                    const targetWidth = diagonal * 0.80;

                    // Get the watermark text
                    const text = this.getFullWatermarkText();

                    // Approximate character width ratio (Helvetica bold averages ~0.6 of font size)
                    const charWidthRatio = 0.55;

                    // Calculate font size: targetWidth = textLength * fontSize * charWidthRatio
                    // fontSize = targetWidth / (textLength * charWidthRatio)
                    let fontSize = targetWidth / (text.length * charWidthRatio);

                    // Clamp between reasonable bounds (like actual watermark: min 10, max 72 scaled for preview)
                    fontSize = Math.max(12, Math.min(fontSize, 48));

                    return Math.round(fontSize);
                },

                formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                },

                getPreviewPosition(position) {
                    const padding = 10; // pixels from edge

                    switch(position) {
                        case 'top-left':
                            return { left: padding + 'px', top: padding + 'px' };
                        case 'top-center':
                            return { left: '50%', top: padding + 'px', transform: 'translateX(-50%)' };
                        case 'top-right':
                            return { right: padding + 'px', top: padding + 'px' };
                        case 'center':
                            return { left: '50%', top: '50%', transform: 'translate(-50%, -50%)' };
                        case 'bottom-left':
                            return { left: padding + 'px', bottom: padding + 'px' };
                        case 'bottom-center':
                            return { left: '50%', bottom: padding + 'px', transform: 'translateX(-50%)' };
                        case 'bottom-right':
                            return { right: padding + 'px', bottom: padding + 'px' };
                        case 'diagonal':
                        default:
                            return { left: '50%', top: '50%', transform: 'translate(-50%, -50%)' };
                    }
                },

                getPreviewPositionAbsolute(position) {
                    const padding = 10; // pixels from edge

                    switch(position) {
                        case 'top-left':
                            return { left: padding + 'px', top: padding + 'px' };
                        case 'top-center':
                            return { left: '50%', top: padding + 'px' };
                        case 'top-right':
                            return { right: padding + 'px', top: padding + 'px' };
                        case 'center':
                            return { left: '50%', top: '50%' };
                        case 'bottom-left':
                            return { left: padding + 'px', bottom: padding + 'px' };
                        case 'bottom-center':
                            return { left: '50%', bottom: padding + 'px' };
                        case 'bottom-right':
                            return { right: padding + 'px', bottom: padding + 'px' };
                        case 'diagonal':
                        default:
                            return { left: '50%', top: '50%' };
                    }
                },

                getPreviewTransform(position, rotation) {
                    // Use rotation directly for CSS preview
                    // Positions that need centering with translate
                    switch(position) {
                        case 'top-center':
                        case 'bottom-center':
                            return `translateX(-50%) rotate(${rotation}deg)`;
                        case 'center':
                        case 'diagonal':
                            return `translate(-50%, -50%) rotate(${rotation}deg)`;
                        default:
                            return `rotate(${rotation}deg)`;
                    }
                },

                handleFileSelect(event) {
                    const selectedFiles = Array.from(event.target.files);
                    this.addFiles(selectedFiles);
                    event.target.value = ''; // Reset input
                },

                handleDrop(event) {
                    this.isDragging = false;
                    const droppedFiles = Array.from(event.dataTransfer.files);
                    this.addFiles(droppedFiles);
                },

                addFiles(newFiles) {
                    this.errorMessage = '';
                    this.successMessage = '';

                    for (const file of newFiles) {
                        // Validate file type
                        if (!file.name.toLowerCase().endsWith('.pdf')) {
                            this.errorMessage = `"${file.name}" is not a PDF file. Only PDF files are allowed.`;
                            continue;
                        }

                        // Validate file size
                        if (file.size > maxFileSize) {
                            this.errorMessage = `"${file.name}" exceeds the maximum file size of ${this.formatFileSize(maxFileSize)}.`;
                            continue;
                        }

                        // Check for duplicates
                        const exists = this.files.some(f => f.name === file.name && f.size === file.size);
                        if (exists) {
                            continue;
                        }

                        // Add file to queue
                        this.files.push({
                            id: ++this.fileIdCounter,
                            file: file,
                            name: file.name,
                            size: file.size,
                            status: 'pending',
                            progress: 0,
                            jobId: null,
                            jobUrl: null
                        });

                        // Render preview for the first added file
                        if (this.files.length === 1 || !this.previewReady) {
                            this.renderPdfPreview(file);
                        }
                    }
                },

                async renderPdfPreview(file) {
                    if (this.previewLoading) return;

                    this.previewLoading = true;
                    this.previewReady = false;
                    this.currentPreviewFile = file;

                    try {
                        const arrayBuffer = await file.arrayBuffer();
                        const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
                        const page = await pdf.getPage(1);

                        const canvas = this.$refs.pdfCanvas;
                        const context = canvas.getContext('2d');

                        // Calculate scale to fit the container
                        const containerWidth = 340;
                        const containerHeight = containerWidth * (11 / 8.5); // Letter aspect ratio

                        const viewport = page.getViewport({ scale: 1 });
                        const scaleX = containerWidth / viewport.width;
                        const scaleY = containerHeight / viewport.height;
                        const scale = Math.min(scaleX, scaleY);

                        const scaledViewport = page.getViewport({ scale: scale });

                        canvas.width = scaledViewport.width;
                        canvas.height = scaledViewport.height;

                        await page.render({
                            canvasContext: context,
                            viewport: scaledViewport
                        }).promise;

                        this.previewReady = true;
                    } catch (error) {
                        console.error('Failed to render PDF preview:', error);
                        this.previewReady = false;
                    } finally {
                        this.previewLoading = false;
                    }
                },

                removeFile(index) {
                    const removedFile = this.files[index];
                    this.files.splice(index, 1);

                    if (this.files.length === 0) {
                        this.errorMessage = '';
                        this.successMessage = '';
                        this.previewReady = false;
                        this.currentPreviewFile = null;
                    } else if (removedFile === this.currentPreviewFile && this.files.length > 0) {
                        // If we removed the previewed file, preview the first remaining file
                        this.renderPdfPreview(this.files[0].file);
                    }
                },

                clearFiles() {
                    this.files = [];
                    this.errorMessage = '';
                    this.successMessage = '';
                    this.uploadedCount = 0;
                    this.previewReady = false;
                    this.currentPreviewFile = null;
                },

                async startUpload() {
                    if (!this.canUpload) return;

                    this.isUploading = true;
                    this.errorMessage = '';
                    this.successMessage = '';
                    this.uploadedCount = 0;

                    // Upload files sequentially
                    for (let i = 0; i < this.files.length; i++) {
                        const fileItem = this.files[i];

                        if (fileItem.status !== 'pending') continue;

                        try {
                            await this.uploadFile(fileItem);
                            this.uploadedCount++;
                        } catch (error) {
                            console.error('Upload failed for', fileItem.name, error);
                        }
                    }

                    this.isUploading = false;

                    // Get successfully uploaded job IDs
                    const successfulJobs = this.files.filter(f => f.status === 'done' && f.jobId);
                    const failCount = this.files.filter(f => f.status === 'error').length;

                    if (successfulJobs.length > 0) {
                        // Redirect to batch results page
                        const jobIds = successfulJobs.map(f => f.jobId).join(',');
                        window.location.href = `{{ route('jobs.batch') }}?ids=${jobIds}`;
                    } else if (failCount > 0) {
                        this.errorMessage = `Failed to upload ${failCount} file${failCount > 1 ? 's' : ''}.`;
                    }
                },

                uploadFile(fileItem) {
                    return new Promise((resolve, reject) => {
                        const formData = new FormData();
                        formData.append('pdf_file', fileItem.file);
                        formData.append('iso', this.iso);
                        formData.append('lender', this.lender);
                        formData.append('font_size', this.fontSize);
                        formData.append('color', this.color);
                        formData.append('opacity', this.opacity);
                        formData.append('position', this.position);
                        formData.append('rotation', this.rotation);

                        const xhr = new XMLHttpRequest();

                        xhr.upload.addEventListener('progress', (e) => {
                            if (e.lengthComputable) {
                                fileItem.progress = Math.round((e.loaded / e.total) * 100);
                            }
                        });

                        xhr.addEventListener('load', () => {
                            if (xhr.status >= 200 && xhr.status < 300) {
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    fileItem.status = 'done';
                                    fileItem.progress = 100;
                                    fileItem.jobId = response.job?.id;
                                    fileItem.jobUrl = response.job?.url;
                                    resolve(response);
                                } catch (e) {
                                    fileItem.status = 'error';
                                    reject(new Error('Invalid response'));
                                }
                            } else {
                                fileItem.status = 'error';
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    reject(new Error(response.message || 'Upload failed'));
                                } catch (e) {
                                    reject(new Error('Upload failed'));
                                }
                            }
                        });

                        xhr.addEventListener('error', () => {
                            fileItem.status = 'error';
                            reject(new Error('Network error'));
                        });

                        fileItem.status = 'uploading';
                        fileItem.progress = 0;

                        xhr.open('POST', '{{ route('jobs.store') }}');
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                        xhr.setRequestHeader('Accept', 'application/json');
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        xhr.send(formData);
                    });
                }
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
