<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8" x-data="batchStatus()">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Batch Upload Results</h1>
                    <p class="text-gray-500 mt-1">
                        <span x-text="completedCount"></span> of <span x-text="totalCount"></span> files processed
                    </p>
                </div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Upload More Files
                </a>
            </div>
        </div>

        <!-- Overall Progress -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8" x-show="hasProcessing">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary-600"></div>
                    <span class="text-sm font-medium text-gray-700">Processing files...</span>
                </div>
                <span class="text-sm text-gray-500" x-text="completedCount + ' / ' + totalCount + ' complete'"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-primary-600 h-2 rounded-full transition-all duration-500"
                     :style="'width: ' + progressPercent + '%'"></div>
            </div>
        </div>

        <!-- Success Banner -->
        <div class="bg-green-50 border border-green-200 rounded-2xl p-6 mb-8" x-show="allComplete && !hasFailed" x-cloak>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-green-800">All files processed successfully!</h3>
                    <p class="text-sm text-green-700 mt-1">Your watermarked PDFs are ready for download.</p>
                </div>
                <div class="ml-auto">
                    <button @click="downloadAll()"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-xl font-medium text-sm hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download All
                    </button>
                </div>
            </div>
        </div>

        <!-- Jobs Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($jobs as $index => $job)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden"
                     x-data="{ job: jobs[{{ $index }}] }">
                    <!-- Job Header -->
                    <div class="px-5 py-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate" title="{{ $job->original_filename }}">
                                        {{ $job->original_filename }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $job->getFormattedFileSize() }}
                                        <span x-show="job.page_count"> &bull; <span x-text="job.page_count"></span> pages</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job Status -->
                    <div class="px-5 py-4">
                        <!-- Pending/Processing State -->
                        <template x-if="job.status === 'pending' || job.status === 'processing'">
                            <div class="flex items-center justify-center py-6">
                                <div class="text-center">
                                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary-600 mx-auto mb-3"></div>
                                    <p class="text-sm font-medium text-gray-700" x-text="job.status === 'pending' ? 'Waiting in queue...' : 'Processing...'"></p>
                                </div>
                            </div>
                        </template>

                        <!-- Done State -->
                        <template x-if="job.status === 'done'">
                            <div>
                                <div class="flex items-center justify-center py-4 mb-4">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a :href="'/jobs/' + job.id + '/download'"
                                       class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-green-600 text-white rounded-xl font-medium text-sm hover:bg-green-700 transition-colors">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Download
                                    </a>
                                    <a :href="'/jobs/' + job.id + '/preview'"
                                       target="_blank"
                                       class="inline-flex justify-center items-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium text-sm hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </template>

                        <!-- Failed State -->
                        <template x-if="job.status === 'failed'">
                            <div>
                                <div class="flex items-center justify-center py-4 mb-3">
                                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="bg-red-50 rounded-xl p-3 mb-3" x-show="job.error_message">
                                    <p class="text-xs text-red-700 line-clamp-2" x-text="job.error_message"></p>
                                </div>
                                <a :href="'/jobs/' + job.id"
                                   class="block w-full text-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium text-sm hover:bg-gray-50 transition-colors">
                                    View Details
                                </a>
                            </div>
                        </template>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Bottom Actions -->
        <div class="mt-8 flex items-center justify-between">
            <a href="{{ route('jobs.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium flex items-center">
                View all jobs
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 transition-colors shadow-lg shadow-primary-600/25">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Upload More Files
            </a>
        </div>
    </div>

    <script>
        function batchStatus() {
            const initialJobs = @json($jobsData);

            return {
                jobs: initialJobs,
                polling: null,

                get totalCount() {
                    return this.jobs.length;
                },

                get completedCount() {
                    return this.jobs.filter(j => j.status === 'done' || j.status === 'failed').length;
                },

                get progressPercent() {
                    if (this.totalCount === 0) return 0;
                    return Math.round((this.completedCount / this.totalCount) * 100);
                },

                get hasProcessing() {
                    return this.jobs.some(j => j.status === 'pending' || j.status === 'processing');
                },

                get allComplete() {
                    return this.jobs.every(j => j.status === 'done' || j.status === 'failed');
                },

                get hasFailed() {
                    return this.jobs.some(j => j.status === 'failed');
                },

                init() {
                    if (this.hasProcessing) {
                        this.startPolling();
                    }
                },

                startPolling() {
                    this.polling = setInterval(() => this.checkAllStatuses(), 3000);
                },

                async checkAllStatuses() {
                    const pendingJobs = this.jobs.filter(j => j.status === 'pending' || j.status === 'processing');

                    for (const job of pendingJobs) {
                        try {
                            const response = await fetch(`/jobs/${job.id}/status`);
                            const data = await response.json();

                            job.status = data.status;
                            job.error_message = data.error_message;
                            job.can_download = data.can_download;

                            // Get page count if done
                            if (data.status === 'done') {
                                const detailResponse = await fetch(`/jobs/${job.id}`, {
                                    headers: { 'Accept': 'application/json' }
                                });
                                if (detailResponse.ok) {
                                    // Page count will be available after refresh
                                }
                            }
                        } catch (error) {
                            console.error('Error checking status for job', job.id, error);
                        }
                    }

                    // Stop polling if all jobs are complete
                    if (this.allComplete && this.polling) {
                        clearInterval(this.polling);
                        this.polling = null;
                    }
                },

                downloadAll() {
                    const doneJobs = this.jobs.filter(j => j.status === 'done' && j.can_download);
                    doneJobs.forEach((job, index) => {
                        setTimeout(() => {
                            const link = document.createElement('a');
                            link.href = `/jobs/${job.id}/download`;
                            link.download = '';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        }, index * 500); // Stagger downloads by 500ms
                    });
                }
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-app-layout>
