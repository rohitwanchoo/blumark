<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tamper Detection Report - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="min-h-screen py-12 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            {{-- Report Header --}}
            <div class="bg-white shadow sm:rounded-lg overflow-hidden mb-6">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-lg leading-6 font-medium text-gray-900">Tamper Detection Report</h1>
                            <p class="mt-1 text-sm text-gray-500">{{ $filename }}</p>
                        </div>
                        <div>
                            @if($report['summary']['tampered'])
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <svg class="mr-1.5 h-4 w-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    Tampering Detected
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="mr-1.5 h-4 w-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Document Appears Authentic
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="px-4 py-5 sm:px-6">
                    <dl class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Analyzed</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($report['summary']['analyzed_at'])->format('M d, Y g:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Confidence</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $report['summary']['confidence'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Warnings</dt>
                            <dd class="mt-1 text-sm {{ $report['summary']['warning_count'] > 0 ? 'text-yellow-600' : 'text-gray-900' }}">{{ $report['summary']['warning_count'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Errors</dt>
                            <dd class="mt-1 text-sm {{ $report['summary']['error_count'] > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $report['summary']['error_count'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Recommendation --}}
            <div class="bg-{{ $report['summary']['tampered'] ? 'red' : 'green' }}-50 rounded-lg p-4 mb-6">
                <div class="flex">
                    <svg class="h-5 w-5 text-{{ $report['summary']['tampered'] ? 'red' : 'green' }}-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-{{ $report['summary']['tampered'] ? 'red' : 'green' }}-800">Recommendation</h3>
                        <p class="mt-1 text-sm text-{{ $report['summary']['tampered'] ? 'red' : 'green' }}-700">{{ $report['recommendation'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Detailed Checks --}}
            <div class="bg-white shadow sm:rounded-lg overflow-hidden mb-6">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Detailed Analysis</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($report['analysis']['checks'] as $checkName => $check)
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        @if($check['status'] === 'passed')
                                            <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @elseif($check['status'] === 'warning')
                                            <svg class="h-5 w-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                        @elseif($check['status'] === 'failed')
                                            <svg class="h-5 w-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @endif
                                        <h3 class="text-sm font-medium text-gray-900">{{ ucwords(str_replace('_', ' ', $checkName)) }}</h3>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600 ml-7">{{ $check['message'] ?? 'No details available' }}</p>

                                    @if(!empty($check['issues']))
                                        <ul class="mt-2 ml-7 text-sm text-gray-500 list-disc list-inside">
                                            @foreach($check['issues'] as $issue)
                                                <li>{{ $issue }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $check['status'] === 'passed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $check['status'] === 'warning' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $check['status'] === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $check['status'] === 'error' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($check['status']) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Removal Signatures --}}
            @if(!empty($report['removal_signatures']))
                <div class="bg-white shadow sm:rounded-lg overflow-hidden mb-6">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Editing Tool Signatures</h2>
                        <p class="mt-1 text-sm text-gray-500">Detected signatures of tools that may have been used to modify the document</p>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($report['removal_signatures'] as $signature)
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $signature['tool'] }}</p>
                                        <p class="text-sm text-gray-500">{{ $signature['message'] }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $signature['confidence'] === 'high' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $signature['confidence'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $signature['confidence'] === 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst($signature['confidence']) }} confidence
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Warnings & Errors --}}
            @if(!empty($report['analysis']['warnings']) || !empty($report['analysis']['errors']))
                <div class="bg-white shadow sm:rounded-lg overflow-hidden mb-6">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Warnings & Errors</h2>
                    </div>
                    <div class="px-4 py-5 sm:px-6">
                        @if(!empty($report['analysis']['warnings']))
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-yellow-800 mb-2">Warnings</h3>
                                <ul class="text-sm text-yellow-700 list-disc list-inside space-y-1">
                                    @foreach($report['analysis']['warnings'] as $warning)
                                        <li>{{ $warning }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(!empty($report['analysis']['errors']))
                            <div>
                                <h3 class="text-sm font-medium text-red-800 mb-2">Errors</h3>
                                <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
                                    @foreach($report['analysis']['errors'] as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Fingerprint Info --}}
            @if($fingerprint)
                <div class="bg-white shadow sm:rounded-lg overflow-hidden mb-6">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Document Fingerprint</h2>
                    </div>
                    <div class="px-4 py-5 sm:px-6">
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Unique Marker</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $fingerprint->unique_marker }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Created</dt>
                                <dd class="text-sm text-gray-900">{{ $fingerprint->created_at->format('M d, Y g:i A') }}</dd>
                            </div>
                            @if($fingerprint->recipient_email)
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Recipient</dt>
                                    <dd class="text-sm text-gray-900">{{ $fingerprint->recipient_email }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex space-x-4">
                <a href="{{ url('/verify') }}" class="flex-1 flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Verify Another Document
                </a>
                <a href="{{ url('/') }}" class="flex-1 flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
