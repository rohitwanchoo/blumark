@extends('layouts.admin')

@section('title', 'OCR Test History')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li>
                            <a href="{{ route('admin.ocr.index') }}" class="text-gray-400 hover:text-gray-500">
                                OCR Testing
                            </a>
                        </li>
                        <li>
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </li>
                        <li>
                            <span class="text-gray-700">Test History</span>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-semibold text-gray-900">OCR Test History</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $job->original_filename }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.ocr.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Back to OCR Testing
                </a>
                <a href="{{ route('jobs.show', $job) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    View Job
                </a>
            </div>
        </div>

        {{-- Job Info --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Job ID</dt>
                        <dd class="mt-1 text-sm text-gray-900">#{{ $job->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $job->status }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $job->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Tests</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $tests->count() }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Test History Timeline --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Test Timeline</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                @if($tests->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No test results</h3>
                        <p class="mt-1 text-sm text-gray-500">Run an OCR test on this document to see results here.</p>
                    </div>
                @else
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @foreach($tests as $index => $test)
                                <li>
                                    <div class="relative pb-8">
                                        @if(!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                                                    {{ $test->watermark_detected ? 'bg-green-500' : ($test->watermark_detected === null ? 'bg-gray-400' : 'bg-red-500') }}">
                                                    @if($test->watermark_detected)
                                                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    @elseif($test->watermark_detected === null)
                                                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">
                                                            OCR Test #{{ $tests->count() - $index }}
                                                        </p>
                                                        <p class="text-sm text-gray-500">
                                                            {{ $test->created_at->diffForHumans() }}
                                                        </p>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                            {{ $test->ocr_engine }}
                                                        </span>
                                                        <button type="button" onclick="showDetails({{ $test->id }})" class="text-sm text-indigo-600 hover:text-indigo-900">
                                                            View Details
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="mt-2 grid grid-cols-3 gap-4">
                                                    <div>
                                                        <dt class="text-xs font-medium text-gray-500">Watermark Detected</dt>
                                                        <dd class="mt-1">
                                                            @if($test->watermark_detected === null)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">N/A</span>
                                                            @elseif($test->watermark_detected)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Yes</span>
                                                            @else
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">No</span>
                                                            @endif
                                                        </dd>
                                                    </div>
                                                    <div>
                                                        <dt class="text-xs font-medium text-gray-500">Confidence</dt>
                                                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($test->confidence_score * 100, 1) }}%</dd>
                                                    </div>
                                                    <div>
                                                        <dt class="text-xs font-medium text-gray-500">Processing Time</dt>
                                                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($test->processing_time_ms) }}ms</dd>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Test Details Modal --}}
<div id="detailsModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50" onclick="closeModal()">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[80vh] overflow-hidden" onclick="event.stopPropagation()">
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Test Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 overflow-y-auto max-h-[60vh]">
                <pre id="detailsContent" class="text-sm text-gray-700 whitespace-pre-wrap font-mono bg-gray-50 p-4 rounded"></pre>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const testDetails = @json($tests->keyBy('id')->map(fn($t) => $t->extracted_text));

    function showDetails(testId) {
        const text = testDetails[testId] || 'No text extracted';
        document.getElementById('detailsContent').textContent = text;
        document.getElementById('detailsModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('detailsModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
</script>
@endpush
@endsection
