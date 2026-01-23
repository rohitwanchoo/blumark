@extends('layouts.admin')

@section('title', 'OCR Test Result')

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
                            <span class="text-gray-700">Test Result</span>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-semibold text-gray-900">OCR Test Result</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $job->original_filename }}</p>
            </div>
            <a href="{{ route('admin.ocr.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Back to OCR Testing
            </a>
        </div>

        {{-- Test Summary --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Test Summary</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">OCR Engine</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $testResult->ocr_engine }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Watermark Detection</dt>
                        <dd class="mt-1">
                            @if($testResult->watermark_detected === null)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    N/A
                                </span>
                            @elseif($testResult->watermark_detected)
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
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Confidence Score</dt>
                        <dd class="mt-1">
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min(100, $testResult->confidence_score * 100) }}%"></div>
                                </div>
                                <span class="text-sm text-gray-900 font-medium">{{ number_format($testResult->confidence_score * 100, 1) }}%</span>
                            </div>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Processing Time</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($testResult->processing_time_ms) }}ms</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Expected Watermark --}}
        @if($watermarkText)
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Expected Watermark Text</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="font-mono text-sm text-gray-700">{{ $watermarkText }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Extracted Text --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Extracted Text</h3>
                <div class="text-sm text-gray-500">
                    Word Count: <span class="font-medium text-gray-700">{{ str_word_count($testResult->extracted_text) }}</span>
                </div>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div class="bg-gray-50 rounded-lg p-4 max-h-96 overflow-y-auto">
                    <pre class="font-mono text-xs text-gray-700 whitespace-pre-wrap">{{ $testResult->extracted_text ?: 'No text was extracted' }}</pre>
                </div>
            </div>
        </div>

        {{-- Job Details --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Watermark Job Details</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Job ID</dt>
                        <dd class="mt-1 text-sm text-gray-900">#{{ $job->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Original Filename</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $job->original_filename }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $job->created_at->format('M d, Y H:i:s') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $job->status }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
