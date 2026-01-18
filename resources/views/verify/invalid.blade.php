<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Failed - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-lg">
            {{-- Verification Failed Card --}}
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                {{-- Status Header --}}
                <div class="bg-red-500 px-4 py-5 sm:px-6">
                    <div class="flex items-center">
                        <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="ml-4">
                            <h3 class="text-lg leading-6 font-medium text-white">Verification Failed</h3>
                            <p class="mt-1 text-sm text-red-100">{{ $message ?? 'The document could not be verified.' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Details --}}
                <div class="px-4 py-5 sm:px-6">
                    <dl class="space-y-4">
                        <div class="sm:flex sm:justify-between">
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ ucfirst(str_replace('_', ' ', $status ?? 'unknown')) }}
                                </span>
                            </dd>
                        </div>

                        @if(isset($filename))
                            <div class="sm:flex sm:justify-between">
                                <dt class="text-sm font-medium text-gray-500">Uploaded File</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0">{{ $filename }}</dd>
                            </div>
                        @endif
                    </dl>

                    {{-- Possible Reasons --}}
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Possible Reasons</h4>
                        <ul class="text-sm text-gray-600 space-y-2">
                            @if($status === 'invalid' || $status === 'not_found')
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    The document was not watermarked through this system
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    The verification token is incorrect or expired
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    The document may be a copy without embedded verification data
                                </li>
                            @endif

                            @if($status === 'orphaned')
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    The original document record has been deleted from the system
                                </li>
                            @endif

                            @if($status === 'invalid_signature')
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    The QR code signature verification failed - the code may have been tampered with
                                </li>
                            @endif

                            @if($status === 'tampered')
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    The document appears to have been modified after watermarking
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex space-x-4">
                <a href="{{ url('/verify') }}" class="flex-1 flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Try Again
                </a>
                <a href="{{ route('dashboard') }}" class="flex-1 flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Back to Dashboard
                </a>
            </div>

            {{-- Help Text --}}
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-gray-800">Need Help?</h3>
                        <div class="mt-2 text-sm text-gray-600">
                            <p>If you believe this document should be verified, please contact the document issuer or the person who shared it with you. They can provide you with the correct verification token or a properly watermarked document.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
