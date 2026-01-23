<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8" x-data="distributionStatus()">
        <!-- Page Header -->
        <div class="mb-8">
            <a href="{{ route('distributions.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Submissions
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $distribution->name ?? $distribution->source_filename }}</h1>
                    @php $sourceFiles = $distribution->getSourceFilesArray(); @endphp
                    <p class="text-gray-500 mt-1">
                        Created {{ $distribution->created_at->format('M j, Y g:i A') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    @if($distribution->isCompleted() || $distribution->items->where('status', 'done')->count() > 0)
                        <a href="{{ route('distributions.download-all', $distribution) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download All (ZIP)
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Progress Card -->
        @if(!$distribution->isCompleted())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Processing</h3>
                        <p class="text-sm text-gray-500">Watermarking documents...</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <svg class="w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="progress + '%'">0%</span>
                        </span>
                    </div>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="h-3 rounded-full transition-all duration-500 bg-blue-500"
                         :style="'width: ' + progress + '%'"></div>
                </div>
            </div>
        </div>
        @endif

        <!-- Bulk Actions -->
        @if($distribution->isCompleted() || $distribution->items->where('status', 'done')->count() > 0)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Send to All Lenders</h3>
                    <p class="text-sm text-gray-500 mt-1">Send documents to all lenders who haven't received them yet</p>
                </div>
                <div class="p-6">
                    <!-- Email Template Info -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm text-gray-600">
                                Email Template:
                                @if($distribution->emailTemplate)
                                    <span class="font-medium text-gray-900">{{ $distribution->emailTemplate->name }}</span>
                                @else
                                    <span class="font-medium text-gray-900">Default System Email</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <form action="{{ route('distributions.send-all', $distribution) }}" method="POST">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="send_via" value="email_attachment" checked class="text-primary-600 focus:ring-primary-500">
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">Email with Attachment</p>
                                        <p class="text-sm text-gray-500">Send PDF attached to email</p>
                                    </div>
                                </label>
                            </div>
                            <div class="flex-1">
                                <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="send_via" value="email_link" class="text-primary-600 focus:ring-primary-500">
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">Email with Download Link</p>
                                        <p class="text-sm text-gray-500">Send secure link to download</p>
                                    </div>
                                </label>
                            </div>
                            <div class="sm:self-end">
                                <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                    Send to All
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @php
            $hasMultipleFiles = count($sourceFiles) > 1;
            $itemsByLender = $distribution->items->groupBy(fn($item) => $item->getLenderCompanyName());
        @endphp

        <!-- Lenders -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Lenders</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $itemsByLender->count() }} {{ $itemsByLender->count() === 1 ? 'lender' : 'lenders' }} receiving {{ count($sourceFiles) }} {{ count($sourceFiles) === 1 ? 'document' : 'documents' }}</p>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($itemsByLender as $lenderName => $lenderItems)
                    @php $firstItem = $lenderItems->first(); @endphp
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $lenderName }}</h4>
                                <p class="text-sm text-gray-500">{{ $firstItem->getLenderEmail() }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                @php
                                    $allDone = $lenderItems->every(fn($i) => $i->watermarkJob?->status === 'done');
                                    $anyProcessing = $lenderItems->contains(fn($i) => $i->watermarkJob?->status === 'processing');
                                    $allSent = $lenderItems->every(fn($i) => $i->isSent());
                                @endphp

                                @if($allDone)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Ready</span>
                                @elseif($anyProcessing)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Processing
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                @endif

                                @if($allSent)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Sent
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($hasMultipleFiles)
                            <div class="mt-3 space-y-2">
                                @foreach($lenderItems as $item)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg text-sm">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="text-gray-700">{{ $item->getSourceFilename() }}</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            @if($item->watermarkJob?->status === 'done')
                                                <a href="{{ route('distributions.item.download', ['distribution' => $distribution, 'item' => $item]) }}" class="text-primary-600 hover:text-primary-800 text-xs font-medium">Download</a>
                                            @endif
                                            @if($item->isSent())
                                                <span class="text-xs text-green-600">Sent</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="mt-3 flex items-center gap-3">
                                @if($allDone)
                                    <a href="{{ route('distributions.item.download', ['distribution' => $distribution, 'item' => $firstItem]) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">Download PDF</a>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Delete Distribution -->
        <div class="mt-8 pt-8 border-t border-gray-200">
            <form action="{{ route('distributions.destroy', $distribution) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this distribution? All associated files will be deleted.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                    Delete this distribution
                </button>
            </form>
        </div>

        @php
            $itemsData = $distribution->items->map(function($item) {
                return [
                    'id' => $item->id,
                    'source_filename' => $item->getSourceFilename(),
                    'source_file_index' => $item->source_file_index,
                    'status' => $item->watermarkJob?->status ?? $item->status,
                    'sent' => $item->isSent(),
                    'sent_via' => $item->sent_via,
                    'can_download' => $item->canDownload(),
                    'can_send' => $item->canSend(),
                ];
            })->values();
        @endphp
        <script>
            function distributionStatus() {
                return {
                    status: '{{ $distribution->status }}',
                    progress: {{ $distribution->getProgressPercentage() }},
                    processedCount: {{ $distribution->processed_count }},
                    failedCount: {{ $distribution->failed_count }},
                    totalLenders: {{ $distribution->total_lenders }},
                    items: @json($itemsData),
                    polling: null,
                    init() {
                        if (this.status === 'processing' || this.status === 'pending') {
                            this.startPolling();
                        }
                    },
                    startPolling() {
                        this.polling = setInterval(() => this.fetchStatus(), 3000);
                    },
                    stopPolling() {
                        if (this.polling) {
                            clearInterval(this.polling);
                            this.polling = null;
                        }
                    },
                    async fetchStatus() {
                        try {
                            const response = await fetch('{{ route("distributions.status", $distribution) }}');
                            const data = await response.json();

                            this.status = data.status;
                            this.progress = data.progress;
                            this.processedCount = data.processed_count;
                            this.failedCount = data.failed_count;
                            this.items = data.items;

                            if (data.status === 'completed' || data.status === 'failed') {
                                this.stopPolling();
                                // Reload page to show completed state
                                window.location.reload();
                            }
                        } catch (error) {
                            console.error('Failed to fetch status:', error);
                        }
                    }
                }
            }
        </script>
    </div>
</x-app-layout>
