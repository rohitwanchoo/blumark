<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Verified - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-lg">
            {{-- Verification Status Card --}}
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                {{-- Status Header --}}
                @if($result['status'] === 'verified')
                    <div class="bg-green-500 px-4 py-5 sm:px-6">
                        <div class="flex items-center">
                            <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="ml-4">
                                <h3 class="text-lg leading-6 font-medium text-white">Document Verified</h3>
                                <p class="mt-1 text-sm text-green-100">This document is authentic and unmodified.</p>
                            </div>
                        </div>
                    </div>
                @elseif($result['status'] === 'modified')
                    <div class="bg-yellow-500 px-4 py-5 sm:px-6">
                        <div class="flex items-center">
                            <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div class="ml-4">
                                <h3 class="text-lg leading-6 font-medium text-white">Document Modified</h3>
                                <p class="mt-1 text-sm text-yellow-100">This document has been modified since watermarking.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-blue-500 px-4 py-5 sm:px-6">
                        <div class="flex items-center">
                            <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="ml-4">
                                <h3 class="text-lg leading-6 font-medium text-white">Document Found</h3>
                                <p class="mt-1 text-sm text-blue-100">{{ $result['message'] ?? 'Document information retrieved.' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Document Details --}}
                <div class="px-4 py-5 sm:px-6">
                    <dl class="space-y-4">
                        @if(isset($uploaded_filename))
                            <div class="sm:flex sm:justify-between">
                                <dt class="text-sm font-medium text-gray-500">Uploaded File</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0">{{ $uploaded_filename }}</dd>
                            </div>
                        @endif

                        @if($fingerprint)
                            <div class="sm:flex sm:justify-between">
                                <dt class="text-sm font-medium text-gray-500">Verification Token</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 font-mono text-xs">{{ Str::limit($fingerprint->verification_token, 20) }}</dd>
                            </div>

                            <div class="sm:flex sm:justify-between">
                                <dt class="text-sm font-medium text-gray-500">Unique Marker</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 font-mono">{{ $fingerprint->unique_marker }}</dd>
                            </div>

                            <div class="sm:flex sm:justify-between">
                                <dt class="text-sm font-medium text-gray-500">Issued Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0">{{ $fingerprint->created_at->format('M d, Y \a\t g:i A') }}</dd>
                            </div>

                            @if($fingerprint->recipient_email)
                                <div class="sm:flex sm:justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Recipient</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0">
                                        {{ $fingerprint->recipient_name ?? '' }}
                                        <span class="text-gray-500">({{ $fingerprint->recipient_email }})</span>
                                    </dd>
                                </div>
                            @endif

                            @if($fingerprint->verified_at)
                                <div class="sm:flex sm:justify-between">
                                    <dt class="text-sm font-medium text-gray-500">First Verified</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0">{{ $fingerprint->verified_at->format('M d, Y \a\t g:i A') }}</dd>
                                </div>
                            @endif
                        @endif

                        @if($job)
                            <div class="border-t border-gray-200 pt-4 space-y-4">
                                @if(isset($job->settings['iso']) && $job->settings['iso'])
                                    <div class="sm:flex sm:justify-between">
                                        <dt class="text-sm font-medium text-gray-500">ISO</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 font-semibold">{{ $job->settings['iso'] }}</dd>
                                    </div>
                                @endif

                                @if(isset($job->settings['lender']) && $job->settings['lender'])
                                    <div class="sm:flex sm:justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Lender</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 font-semibold">{{ $job->settings['lender'] }}</dd>
                                    </div>
                                @endif

                                <div class="sm:flex sm:justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Original Filename</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0">{{ $job->original_filename }}</dd>
                                </div>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Tamper Analysis (if available) --}}
                @if(isset($tamper_analysis))
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Tamper Analysis</h4>
                        <div class="rounded-md {{ $tamper_analysis['tampered'] ? 'bg-red-50' : 'bg-green-50' }} p-4">
                            <div class="flex">
                                @if($tamper_analysis['tampered'])
                                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Potential Tampering Detected</h3>
                                        <p class="mt-1 text-sm text-red-700">Confidence: {{ round($tamper_analysis['confidence'] * 100) }}%</p>
                                    </div>
                                @else
                                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-green-800">No Tampering Detected</h3>
                                        <p class="mt-1 text-sm text-green-700">Confidence: {{ round($tamper_analysis['confidence'] * 100) }}%</p>
                                    </div>
                                @endif
                            </div>

                            @if(!empty($tamper_analysis['warnings']))
                                <div class="mt-3">
                                    <p class="text-sm font-medium {{ $tamper_analysis['tampered'] ? 'text-red-800' : 'text-yellow-800' }}">Warnings:</p>
                                    <ul class="mt-1 text-sm {{ $tamper_analysis['tampered'] ? 'text-red-700' : 'text-yellow-700' }} list-disc list-inside">
                                        @foreach($tamper_analysis['warnings'] as $warning)
                                            <li>{{ $warning }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex space-x-4">
                <a href="{{ url('/verify') }}" class="flex-1 flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Verify Another
                </a>
                <a href="{{ route('dashboard') }}" class="flex-1 flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Back to Dashboard
                </a>
            </div>

            {{-- Information Note --}}
            <div class="mt-6 bg-blue-50 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">About Document Verification</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>This verification confirms that the document was watermarked through our system and helps detect unauthorized modifications. The unique marker embedded in the document can be traced back to the original recipient.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
