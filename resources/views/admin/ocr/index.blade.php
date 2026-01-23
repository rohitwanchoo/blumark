@extends('layouts.admin')

@section('title', 'OCR Testing')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">OCR Testing</h1>
                <p class="mt-1 text-sm text-gray-600">Test watermark detection using OCR engines on watermarked documents</p>
            </div>
        </div>

        {{-- Available Engines --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Available OCR Engines</h3>
            </div>
            <div class="p-6">
                @if(count($availableEngines) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($availableEngines as $engineKey => $engineName)
                            <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-sm font-medium text-green-900">{{ $engineName }}</h4>
                                    <p class="text-xs text-green-700">Engine: {{ $engineKey }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No OCR Engines Available</h3>
                        <p class="mt-1 text-sm text-gray-500">Please install Tesseract or configure Google Vision API.</p>
                        <div class="mt-4 text-left max-w-md mx-auto bg-gray-50 rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-700 mb-2">To install Tesseract:</p>
                            <code class="text-xs text-gray-600">sudo apt-get install tesseract-ocr tesseract-ocr-eng</code>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Test a Job --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Test a Watermarked Document</h3>
                <p class="mt-1 text-sm text-gray-500">Run OCR on a watermarked PDF to verify the watermark is detectable</p>
            </div>
            <div class="p-6">
                <form action="" method="POST" id="testForm" class="space-y-4">
                    @csrf
                    <div>
                        <label for="job_id" class="block text-sm font-medium text-gray-700">Select Watermark Job</label>
                        <select name="job_id" id="job_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md border">
                            <option value="">-- Select a job --</option>
                            @php
                                $jobs = \App\Models\WatermarkJob::where('status', 'done')
                                    ->whereNotNull('output_path')
                                    ->orderBy('created_at', 'desc')
                                    ->limit(50)
                                    ->get();
                            @endphp
                            @foreach($jobs as $job)
                                <option value="{{ $job->id }}">
                                    {{ Str::limit($job->original_filename, 40) }} - {{ $job->created_at->format('M d, Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="engine" class="block text-sm font-medium text-gray-700">OCR Engine</label>
                        <select name="engine" id="engine" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md border">
                            <option value="">Default ({{ config('watermark.ocr.default_engine', 'tesseract') }})</option>
                            @foreach($availableEngines as $engineKey => $engineName)
                                <option value="{{ $engineKey }}">{{ $engineName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex space-x-3">
                        <button type="submit" id="runTestBtn" disabled
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Run OCR Test
                        </button>
                        <button type="button" id="compareBtn" disabled
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Compare All Engines
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Recent Test Results --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Test Results</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Engine</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Watermark Detected</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Confidence</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Processing Time</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentTests as $test)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ Str::limit($test->watermarkJob?->original_filename ?? 'Unknown', 30) }}
                                    </div>
                                    <div class="text-xs text-gray-500">Job #{{ $test->watermark_job_id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $test->ocr_engine }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($test->watermark_detected === null)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            N/A
                                        </span>
                                    @elseif($test->watermark_detected)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Detected
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            Not Detected
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min(100, $test->confidence_score * 100) }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ number_format($test->confidence_score * 100, 1) }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($test->processing_time_ms) }}ms
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $test->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button type="button" onclick="showExtractedText({{ $test->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        View Text
                                    </button>
                                    <form action="{{ route('admin.ocr.destroy', $test) }}" method="POST" class="inline" onsubmit="return confirm('Delete this test result?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No test results yet</h3>
                                    <p class="mt-1 text-sm text-gray-500">Run an OCR test on a watermarked document to see results here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- How It Works --}}
        <div class="bg-blue-50 rounded-lg p-6">
            <h3 class="text-lg font-medium text-blue-900 mb-4">How OCR Testing Works</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 text-blue-600 font-semibold">1</div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-blue-900">PDF to Images</h4>
                        <p class="mt-1 text-sm text-blue-700">The watermarked PDF is converted to high-resolution images for OCR processing.</p>
                    </div>
                </div>
                <div class="flex">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 text-blue-600 font-semibold">2</div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-blue-900">Text Extraction</h4>
                        <p class="mt-1 text-sm text-blue-700">OCR engine extracts all visible text from the document images.</p>
                    </div>
                </div>
                <div class="flex">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 text-blue-600 font-semibold">3</div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-blue-900">Watermark Detection</h4>
                        <p class="mt-1 text-sm text-blue-700">Extracted text is searched for the expected watermark patterns.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Extracted Text Modal --}}
<div id="textModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50" onclick="closeModal()">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[80vh] overflow-hidden" onclick="event.stopPropagation()">
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Extracted Text</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 overflow-y-auto max-h-[60vh]">
                <pre id="extractedTextContent" class="text-sm text-gray-700 whitespace-pre-wrap font-mono bg-gray-50 p-4 rounded"></pre>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const testResults = @json($recentTests->keyBy('id')->map(fn($t) => $t->extracted_text));

    document.getElementById('job_id').addEventListener('change', function() {
        const hasValue = this.value !== '';
        document.getElementById('runTestBtn').disabled = !hasValue;
        document.getElementById('compareBtn').disabled = !hasValue;

        if (hasValue) {
            document.getElementById('testForm').action = '/admin/ocr/job/' + this.value + '/test';
        }
    });

    document.getElementById('compareBtn').addEventListener('click', function() {
        const jobId = document.getElementById('job_id').value;
        if (jobId) {
            const form = document.getElementById('testForm');
            form.action = '/admin/ocr/job/' + jobId + '/compare';
            form.submit();
        }
    });

    function showExtractedText(testId) {
        const text = testResults[testId] || 'No text extracted';
        document.getElementById('extractedTextContent').textContent = text;
        document.getElementById('textModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('textModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
</script>
@endpush
@endsection
