<x-app-layout>
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8" x-data="watermarkForm()">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-500 mt-1">Welcome back! Upload a PDF to add watermarks.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Jobs</p>
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
                    <h3 class="text-lg font-semibold text-gray-900">Add Watermark to PDF</h3>
                    <p class="text-sm text-gray-500 mt-1">Upload a document and configure your watermark settings</p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('jobs.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- PDF File Upload -->
                        <div class="mb-6">
                            <label for="pdf_file" class="block text-sm font-medium text-gray-700 mb-2">PDF File</label>
                            <div class="relative">
                                <input type="file" name="pdf_file" id="pdf_file" accept=".pdf" required
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 file:cursor-pointer cursor-pointer border border-gray-200 rounded-xl">
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Maximum file size: {{ config('watermark.max_upload_mb') }}MB</p>
                            @error('pdf_file')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ISO & Lender Fields -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="iso" class="block text-sm font-medium text-gray-700 mb-2">ISO</label>
                                <input type="text" name="iso" id="iso" x-model="iso" required maxlength="50"
                                       class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-3 border"
                                       placeholder="Enter ISO">
                                @error('iso')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="lender" class="block text-sm font-medium text-gray-700 mb-2">Lender</label>
                                <input type="text" name="lender" id="lender" x-model="lender" required maxlength="50"
                                       class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm px-4 py-3 border"
                                       placeholder="Enter Lender">
                                @error('lender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Appearance Settings -->
                        <div class="space-y-4 mb-6">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-700">Font Size</label>
                                <span class="text-sm text-gray-500" x-text="fontSize + 'px'"></span>
                            </div>
                            <input type="range" name="font_size" id="font_size" min="8" max="48" x-model="fontSize"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary-600">

                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-700">Opacity</label>
                                <span class="text-sm text-gray-500" x-text="opacity + '%'"></span>
                            </div>
                            <input type="range" name="opacity" id="opacity" min="1" max="100" x-model="opacity"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary-600">

                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-700">Color</label>
                                <div class="flex items-center space-x-2">
                                    <input type="color" name="color" id="color" x-model="color"
                                           class="h-8 w-8 rounded-lg cursor-pointer border border-gray-200 p-0.5">
                                    <span class="text-sm text-gray-500 font-mono" x-text="color"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center px-6 py-3.5 bg-primary-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-primary-700 focus:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200 shadow-lg shadow-primary-600/25">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Upload & Add Watermark
                        </button>
                    </form>
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
                        <div class="flex items-center space-x-1">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            <span class="text-xs text-gray-500">Real-time</span>
                        </div>
                    </div>
                </div>
                <div class="p-6 overflow-hidden">
                    <!-- Preview Document -->
                    <div class="relative bg-white border-2 border-gray-200 rounded-lg shadow-inner mx-auto overflow-hidden" style="aspect-ratio: 8.5/11; max-width: 340px;">
                        <!-- Document lines (simulated content) -->
                        <div class="absolute inset-0 p-8 overflow-hidden pointer-events-none">
                            <div class="space-y-3 mt-6">
                                <div class="h-3 bg-gray-100 rounded w-3/4"></div>
                                <div class="h-2 bg-gray-50 rounded w-full"></div>
                                <div class="h-2 bg-gray-50 rounded w-full"></div>
                                <div class="h-2 bg-gray-50 rounded w-5/6"></div>
                                <div class="h-2 bg-gray-50 rounded w-full"></div>
                                <div class="h-2 bg-gray-50 rounded w-4/5"></div>
                                <div class="h-3 bg-gray-100 rounded w-1/2 mt-6"></div>
                                <div class="h-2 bg-gray-50 rounded w-full"></div>
                                <div class="h-2 bg-gray-50 rounded w-full"></div>
                                <div class="h-2 bg-gray-50 rounded w-3/4"></div>
                                <div class="h-2 bg-gray-50 rounded w-full"></div>
                            </div>
                        </div>

                        <!-- Single diagonal watermark across center -->
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <span class="font-mono font-bold whitespace-nowrap transform -rotate-45"
                                  :style="{
                                      color: color,
                                      opacity: opacity / 100,
                                      fontSize: '12px'
                                  }"
                                  x-text="getWatermarkText()">
                            </span>
                        </div>

                        <!-- Corner fold effect -->
                        <div class="absolute top-0 right-0 w-6 h-6 bg-gradient-to-bl from-gray-200 to-transparent"></div>
                    </div>

                    <!-- Preview Info -->
                    <div class="mt-4 p-3 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Watermark layout:</span>
                            <span class="font-semibold text-gray-700">Diagonal text across center</span>
                        </div>
                        <div class="flex items-center justify-between text-xs mt-1">
                            <span class="text-gray-500">Watermark text:</span>
                            <span class="font-mono text-gray-700 truncate max-w-[180px]" x-text="getWatermarkText()"></span>
                        </div>
                        <div class="flex items-center justify-between text-xs mt-1">
                            <span class="text-gray-500">Opacity:</span>
                            <span class="text-gray-700" x-text="opacity + '%'"></span>
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

            <!-- Recent Jobs -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Recent Jobs</h3>
                        <p class="text-sm text-gray-500 mt-1">Your latest watermarking jobs</p>
                    </div>
                    <a href="{{ route('jobs.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium flex items-center">
                        View all
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <div class="p-6">
                    @if($recentJobs->isEmpty())
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900 mb-1">No jobs yet</h4>
                            <p class="text-sm text-gray-500">Upload a PDF to get started!</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($recentJobs as $job)
                                <a href="{{ route('jobs.show', $job) }}" class="block p-4 rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50/50 transition-all duration-200 group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3 min-w-0">
                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-primary-100 transition-colors">
                                                <svg class="w-5 h-5 text-gray-500 group-hover:text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $job->original_filename }}</p>
                                                <p class="text-xs text-gray-500">{{ $job->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            @if($job->status === 'done')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Done
                                                </span>
                                            @elseif($job->status === 'pending' || $job->status === 'processing')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                    <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    {{ ucfirst($job->status) }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Failed
                                                </span>
                                            @endif
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function watermarkForm() {
            const defaults = @json($defaults ?? []);

            return {
                iso: '',
                lender: '',
                fontSize: defaults.font_size || 15,
                color: defaults.color || '#878787',
                opacity: defaults.opacity || 20,

                getWatermarkText() {
                    const isoText = this.iso || 'ISO Name';
                    const lenderText = this.lender || 'Lender Name';
                    return `${isoText} | ${lenderText}`;
                },

                getPreviewFontSize() {
                    // Scale font size for preview (preview is ~340px wide, actual PDF is ~612px)
                    // So we scale by roughly 0.55, with min 6px and max 14px for readability
                    const scaled = Math.round(this.fontSize * 0.35);
                    return Math.max(6, Math.min(14, scaled));
                }
            };
        }
    </script>
</x-app-layout>
