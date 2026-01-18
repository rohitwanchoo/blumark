<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Job Details
            </h2>
            <a href="{{ route('jobs.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                &larr; Back to Jobs
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8" x-data="jobPage()" x-init="startPolling()">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Status Header -->
                <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @if($job->isPending() || $job->isProcessing())
                                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
                            @elseif($job->isDone())
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            @else
                                <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900" x-text="statusText">
                                @if($job->isPending())
                                    Waiting in queue...
                                @elseif($job->isProcessing())
                                    Processing your PDF...
                                @elseif($job->isDone())
                                    Watermark complete!
                                @else
                                    Processing failed
                                @endif
                            </h3>
                            <p class="text-sm text-gray-500">
                                Created {{ $job->created_at->diffForHumans() }}
                                @if($job->processed_at)
                                    &bull; Processed {{ $job->processed_at->diffForHumans() }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $job->getStatusBadgeClass() }}" x-text="status">
                        {{ ucfirst($job->status) }}
                    </span>
                </div>

                <!-- File Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Original File</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="h-8 w-8 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 break-all">{{ $job->original_filename }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $job->getFormattedFileSize() }}
                                        @if($job->page_count)
                                            &bull; {{ $job->page_count }} pages
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Watermark Settings</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <dl class="grid grid-cols-2 gap-2 text-sm">
                                <dt class="text-gray-500">Type:</dt>
                                <dd class="text-gray-900 font-medium">{{ ucfirst($job->settings['type'] ?? 'text') }}</dd>

                                @if(($job->settings['type'] ?? 'text') === 'text')
                                    <dt class="text-gray-500">Text:</dt>
                                    <dd class="text-gray-900 font-medium truncate" title="{{ $job->settings['text'] ?? '' }}">{{ $job->settings['text'] ?? 'N/A' }}</dd>

                                    <dt class="text-gray-500">Font Size:</dt>
                                    <dd class="text-gray-900 font-medium">{{ $job->settings['font_size'] ?? 'N/A' }}</dd>

                                    <dt class="text-gray-500">Color:</dt>
                                    <dd class="text-gray-900 font-medium flex items-center">
                                        <span class="w-4 h-4 rounded mr-2" style="background-color: {{ $job->settings['color'] ?? '#888888' }}"></span>
                                        {{ $job->settings['color'] ?? '#888888' }}
                                    </dd>
                                @else
                                    <dt class="text-gray-500">Scale:</dt>
                                    <dd class="text-gray-900 font-medium">{{ $job->settings['scale'] ?? 50 }}%</dd>
                                @endif

                                <dt class="text-gray-500">Position:</dt>
                                <dd class="text-gray-900 font-medium">{{ ucfirst($job->settings['position'] ?? 'diagonal') }}</dd>

                                <dt class="text-gray-500">Opacity:</dt>
                                <dd class="text-gray-900 font-medium">{{ $job->settings['opacity'] ?? 50 }}%</dd>

                                <dt class="text-gray-500">Rotation:</dt>
                                <dd class="text-gray-900 font-medium">{{ $job->settings['rotation'] ?? 45 }}°</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Document Verification -->
                @if($job->isDone() && $fingerprint)
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Document Verification</h4>
                    <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-indigo-800 font-medium">This document is protected with fraud detection</p>
                                <div class="mt-2 space-y-2">
                                    <div class="flex items-center text-sm">
                                        <span class="text-indigo-600 font-medium w-28">Unique Marker:</span>
                                        <code class="bg-white px-2 py-0.5 rounded text-indigo-900 font-mono text-xs">{{ $fingerprint->unique_marker }}</code>
                                    </div>
                                    <div class="flex items-center text-sm">
                                        <span class="text-indigo-600 font-medium w-28">Verification:</span>
                                        <a href="{{ $fingerprint->getVerificationUrl() }}" target="_blank" class="text-indigo-700 hover:text-indigo-900 underline text-xs font-mono truncate max-w-md">
                                            {{ $fingerprint->getVerificationUrl() }}
                                        </a>
                                        <button onclick="navigator.clipboard.writeText('{{ $fingerprint->getVerificationUrl() }}'); alert('Verification URL copied!');" class="ml-2 text-indigo-500 hover:text-indigo-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-indigo-600">Share this URL to allow anyone to verify this document's authenticity.</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Error Message -->
                @if($job->isFailed() && $job->error_message)
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-red-800">Error Details</h4>
                                <p class="mt-1 text-sm text-red-700">{{ $job->error_message }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-3" x-show="canDownload || {{ $job->isDone() && $job->outputExists() ? 'true' : 'false' }}">
                        <a href="{{ route('jobs.download', $job) }}"
                           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download
                        </a>
                        <a href="{{ route('jobs.preview', $job) }}" target="_blank"
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Preview
                        </a>
                        <button type="button"
                                @click="showShareModal = true"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                            Share
                        </button>
                    </div>

                    <form method="POST" action="{{ route('jobs.destroy', $job) }}" onsubmit="return confirm('Are you sure you want to delete this job? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-white border border-red-300 rounded-md font-semibold text-sm text-red-700 uppercase tracking-widest hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>

                <!-- Existing Shared Links -->
                <div class="mt-6 pt-6 border-t border-gray-200" x-show="sharedLinks.length > 0">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Shared Links</h4>
                    <div class="space-y-2">
                        <template x-for="link in sharedLinks" :key="link.id">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-mono text-gray-600 truncate" x-text="link.url"></span>
                                        <button @click="copyLink(link.url)" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="flex items-center space-x-3 text-xs text-gray-500 mt-1">
                                        <span x-text="'Expires: ' + link.expires_at"></span>
                                        <span x-text="link.download_count + ' downloads'"></span>
                                        <span x-show="link.has_password" class="text-indigo-600">Password protected</span>
                                        <span x-show="!link.is_valid" class="text-red-600">Expired</span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Share Modal -->
        <div x-show="showShareModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showShareModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showShareModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showShareModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

                    <!-- Success State -->
                    <div x-show="shareSuccess" class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Link Created!</h3>
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <input type="text" readonly :value="createdLink" class="w-full text-sm font-mono text-center bg-transparent border-0 focus:ring-0">
                        </div>
                        <div class="flex justify-center space-x-3">
                            <button @click="copyLink(createdLink)" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                                Copy Link
                            </button>
                            <button @click="showShareModal = false; shareSuccess = false;" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-300">
                                Close
                            </button>
                        </div>
                    </div>

                    <!-- Form State -->
                    <div x-show="!shareSuccess">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Create Shareable Link</h3>

                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Name</label>
                                    <input type="text" x-model="shareForm.recipient_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Optional">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Email</label>
                                    <input type="email" x-model="shareForm.recipient_email" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Optional">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Expires In</label>
                                    <select x-model="shareForm.expires_in" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <option value="1">1 day</option>
                                        <option value="3">3 days</option>
                                        <option value="7" selected>7 days</option>
                                        <option value="14">14 days</option>
                                        <option value="30">30 days</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Downloads</label>
                                    <input type="number" x-model="shareForm.max_downloads" min="1" max="100" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Unlimited">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password Protection</label>
                                <input type="text" x-model="shareForm.password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Leave empty for no password">
                            </div>

                            <div x-show="shareForm.recipient_email" class="flex items-center">
                                <input type="checkbox" x-model="shareForm.send_email" id="send_email" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="send_email" class="ml-2 block text-sm text-gray-700">Send email notification to recipient</label>
                            </div>

                            <div x-show="shareError" class="p-3 bg-red-50 rounded-md">
                                <p class="text-sm text-red-700" x-text="shareError"></p>
                            </div>
                        </div>

                        <div class="mt-5 flex justify-end space-x-3">
                            <button @click="showShareModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button @click="createShareLink()" :disabled="isSharing" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50">
                                <span x-show="!isSharing">Create Link</span>
                                <span x-show="isSharing">Creating...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function jobPage() {
            return {
                status: '{{ ucfirst($job->status) }}',
                statusText: '{{ $job->isPending() ? "Waiting in queue..." : ($job->isProcessing() ? "Processing your PDF..." : ($job->isDone() ? "Watermark complete!" : "Processing failed")) }}',
                canDownload: {{ $job->isDone() && $job->outputExists() ? 'true' : 'false' }},
                polling: null,

                // Share functionality
                showShareModal: false,
                sharedLinks: [],
                shareForm: {
                    recipient_name: '',
                    recipient_email: '',
                    expires_in: '7',
                    max_downloads: '',
                    password: '',
                    send_email: false
                },
                isSharing: false,
                shareError: '',
                shareSuccess: false,
                createdLink: '',

                startPolling() {
                    this.loadSharedLinks();
                    if ('{{ $job->status }}' === 'pending' || '{{ $job->status }}' === 'processing') {
                        this.polling = setInterval(() => this.checkStatus(), 3000);
                    }
                },

                async loadSharedLinks() {
                    try {
                        const response = await fetch('{{ route("jobs.shares", $job) }}');
                        if (response.ok) {
                            this.sharedLinks = await response.json();
                        }
                    } catch (e) {
                        console.error('Failed to load shared links:', e);
                    }
                },

                async createShareLink() {
                    this.isSharing = true;
                    this.shareError = '';

                    try {
                        const response = await fetch('{{ route("jobs.share", $job) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                recipient_name: this.shareForm.recipient_name || null,
                                recipient_email: this.shareForm.recipient_email || null,
                                expires_in: parseInt(this.shareForm.expires_in),
                                max_downloads: this.shareForm.max_downloads ? parseInt(this.shareForm.max_downloads) : null,
                                password: this.shareForm.password || null,
                                send_email: this.shareForm.send_email
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.createdLink = data.link;
                            this.shareSuccess = true;
                            this.loadSharedLinks();
                            // Reset form
                            this.shareForm = {
                                recipient_name: '',
                                recipient_email: '',
                                expires_in: '7',
                                max_downloads: '',
                                password: '',
                                send_email: false
                            };
                        } else {
                            this.shareError = data.message || 'Failed to create share link';
                        }
                    } catch (e) {
                        this.shareError = 'An error occurred. Please try again.';
                    }

                    this.isSharing = false;
                },

                copyLink(url) {
                    navigator.clipboard.writeText(url).then(() => {
                        alert('Link copied to clipboard!');
                    });
                },

                async checkStatus() {
                    try {
                        const response = await fetch('{{ route("jobs.status", $job) }}');
                        const data = await response.json();

                        this.status = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                        this.canDownload = data.can_download;

                        if (data.status === 'pending') {
                            this.statusText = 'Waiting in queue...';
                        } else if (data.status === 'processing') {
                            this.statusText = 'Processing your PDF...';
                        } else if (data.status === 'done') {
                            this.statusText = 'Watermark complete!';
                            clearInterval(this.polling);
                            location.reload();
                        } else if (data.status === 'failed') {
                            this.statusText = 'Processing failed';
                            clearInterval(this.polling);
                            location.reload();
                        }
                    } catch (error) {
                        console.error('Error checking status:', error);
                    }
                }
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
