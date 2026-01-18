<x-app-layout>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="batchStatus()" x-init="startPolling()">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $batch->name ?? 'Batch #' . $batch->id }}</h1>
            <p class="text-gray-600 mt-1">{{ $batch->iso }} → {{ $batch->lender }}</p>
        </div>
        <a href="{{ route('batch.index') }}" class="text-gray-600 hover:text-gray-900">
            ← All Batches
        </a>
    </div>

    <!-- Progress Card -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center"
                     :class="{
                         'bg-yellow-100': status === 'pending',
                         'bg-blue-100': status === 'processing',
                         'bg-green-100': status === 'completed',
                         'bg-red-100': status === 'failed'
                     }">
                    <template x-if="status === 'processing'">
                        <svg class="w-6 h-6 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <template x-if="status === 'completed'">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>
                    <template x-if="status === 'failed'">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </template>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 capitalize" x-text="status"></p>
                    <p class="text-sm text-gray-600">
                        <span x-text="processedFiles"></span> of <span x-text="totalFiles"></span> files processed
                    </p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-gray-900" x-text="progress + '%'"></p>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-3">
            <div class="h-3 rounded-full transition-all duration-500"
                 :class="{
                     'bg-blue-600': status === 'processing',
                     'bg-green-600': status === 'completed',
                     'bg-red-600': status === 'failed'
                 }"
                 :style="'width: ' + progress + '%'"></div>
        </div>

        <div x-show="failedFiles > 0" class="mt-4 p-3 bg-red-50 rounded-lg">
            <p class="text-sm text-red-700">
                <span x-text="failedFiles"></span> file(s) failed to process.
            </p>
        </div>
    </div>

    <!-- Download Button -->
    <div x-show="status === 'completed' && (processedFiles - failedFiles) > 0" class="mb-6">
        <a href="{{ route('batch.download', $batch) }}"
           class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download All as ZIP
        </a>
    </div>

    <!-- Files List -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="font-semibold text-gray-900">Files</h2>
        </div>
        <div class="divide-y divide-gray-200">
            <template x-for="job in jobs" :key="job.id">
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                             :class="{
                                 'bg-yellow-100': job.status === 'pending',
                                 'bg-blue-100': job.status === 'processing',
                                 'bg-green-100': job.status === 'done',
                                 'bg-red-100': job.status === 'failed'
                             }">
                            <template x-if="job.status === 'pending'">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                            <template x-if="job.status === 'processing'">
                                <svg class="w-4 h-4 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </template>
                            <template x-if="job.status === 'done'">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                            <template x-if="job.status === 'failed'">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </template>
                        </div>
                        <span class="text-gray-900 truncate" x-text="job.filename"></span>
                    </div>
                    <template x-if="job.status === 'done'">
                        <a :href="'/jobs/' + job.id + '/download'" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                            Download
                        </a>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function batchStatus() {
    return {
        status: '{{ $batch->status }}',
        totalFiles: {{ $batch->total_files }},
        processedFiles: {{ $batch->processed_files }},
        failedFiles: {{ $batch->failed_files }},
        progress: {{ $batch->getProgressPercentage() }},
        jobs: @json($batch->watermarkJobs->map(fn($j) => ['id' => $j->id, 'filename' => $j->original_filename, 'status' => $j->status])),
        polling: null,

        startPolling() {
            if (this.status === 'processing' || this.status === 'pending') {
                this.polling = setInterval(() => this.fetchStatus(), 2000);
            }
        },

        async fetchStatus() {
            try {
                const response = await fetch('{{ route('batch.status', $batch) }}');
                const data = await response.json();

                this.status = data.status;
                this.processedFiles = data.processed_files;
                this.failedFiles = data.failed_files;
                this.progress = data.progress;
                this.jobs = data.jobs;

                if (data.status !== 'processing' && data.status !== 'pending') {
                    clearInterval(this.polling);
                }
            } catch (error) {
                console.error('Failed to fetch status:', error);
            }
        }
    }
}
</script>
</x-app-layout>
