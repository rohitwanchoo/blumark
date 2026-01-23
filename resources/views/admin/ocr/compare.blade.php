@extends('layouts.admin')

@section('title', 'OCR Engine Comparison')

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
                            <span class="text-gray-700">Engine Comparison</span>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-semibold text-gray-900">OCR Engine Comparison</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $job->original_filename }}</p>
            </div>
            <a href="{{ route('admin.ocr.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Back to OCR Testing
            </a>
        </div>

        {{-- Comparison Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($results as $engineName => $result)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6 {{ $result['available'] ? 'bg-green-50' : 'bg-gray-50' }}">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg leading-6 font-medium {{ $result['available'] ? 'text-green-900' : 'text-gray-500' }}">
                                {{ $engineName }}
                            </h3>
                            @if($result['available'])
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Available
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-600">
                                    Unavailable
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($result['available'])
                        <div class="px-4 py-5 sm:p-6">
                            {{-- Stats --}}
                            <dl class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Confidence</dt>
                                    <dd class="mt-1">
                                        <div class="flex items-center">
                                            <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min(100, ($result['confidence'] ?? 0) * 100) }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">{{ number_format(($result['confidence'] ?? 0) * 100, 1) }}%</span>
                                        </div>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Processing Time</dt>
                                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ number_format($result['processing_time_ms'] ?? 0) }}ms</dd>
                                </div>
                            </dl>

                            {{-- Pattern Detection --}}
                            @if(!empty($result['patterns_found']))
                                <div class="mb-4">
                                    <dt class="text-sm font-medium text-gray-500 mb-2">Patterns Detected</dt>
                                    <dd>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($result['patterns_found'] as $pattern)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    {{ Str::limit($pattern, 30) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </dd>
                                </div>
                            @endif

                            {{-- Extracted Text Preview --}}
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-2">Extracted Text</dt>
                                <dd>
                                    <div class="bg-gray-50 rounded p-3 max-h-48 overflow-y-auto">
                                        <pre class="font-mono text-xs text-gray-700 whitespace-pre-wrap">{{ Str::limit($result['text'] ?? 'No text extracted', 500) }}</pre>
                                    </div>
                                    @if(strlen($result['text'] ?? '') > 500)
                                        <button type="button" onclick="showFullText('{{ $engineName }}')" class="mt-2 text-sm text-indigo-600 hover:text-indigo-900">
                                            Show full text
                                        </button>
                                    @endif
                                </dd>
                            </div>
                        </div>
                    @else
                        <div class="px-4 py-5 sm:p-6">
                            <p class="text-sm text-gray-500">{{ $result['error'] ?? 'Engine not available' }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Best Engine Recommendation --}}
        @php
            $availableResults = collect($results)->filter(fn($r) => $r['available']);
            $bestEngine = $availableResults->sortByDesc(fn($r) => $r['confidence'] ?? 0)->keys()->first();
        @endphp

        @if($bestEngine)
        <div class="bg-blue-50 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Recommendation</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>
                            Based on confidence scores, <strong>{{ $bestEngine }}</strong> performed best for this document
                            with {{ number_format(($results[$bestEngine]['confidence'] ?? 0) * 100, 1) }}% confidence.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Full Text Modal --}}
<div id="fullTextModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50" onclick="closeModal()">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[80vh] overflow-hidden" onclick="event.stopPropagation()">
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Full Extracted Text - <span id="modalEngineName"></span></h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 overflow-y-auto max-h-[60vh]">
                <pre id="fullTextContent" class="text-sm text-gray-700 whitespace-pre-wrap font-mono bg-gray-50 p-4 rounded"></pre>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const fullTexts = @json(collect($results)->filter(fn($r) => $r['available'])->map(fn($r) => $r['text'] ?? ''));

    function showFullText(engineName) {
        document.getElementById('modalEngineName').textContent = engineName;
        document.getElementById('fullTextContent').textContent = fullTexts[engineName] || 'No text available';
        document.getElementById('fullTextModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('fullTextModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
</script>
@endpush
@endsection
