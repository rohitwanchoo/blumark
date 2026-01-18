@extends('layouts.admin')

@section('title', 'Leak Detection')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Leak Detection</h1>
            <p class="mt-1 text-sm text-gray-600">Monitor for potential document leaks and unauthorized sharing</p>
        </div>
        <a href="{{ route('admin.audit.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Back to Audit
        </a>
    </div>

    {{-- Upload Document for Investigation --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Investigate Document</h3>
            <p class="mt-1 text-sm text-gray-500">Upload a suspected leaked document to trace its origin</p>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.audit.investigate') }}" method="POST" enctype="multipart/form-data" class="flex items-end space-x-4">
                @csrf
                <div class="flex-1">
                    <label for="document" class="block text-sm font-medium text-gray-700">PDF Document</label>
                    <input type="file" name="document" id="document" accept=".pdf" required
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Investigate
                </button>
            </form>
        </div>
    </div>

    {{-- High Risk Documents --}}
    @if($highRiskJobs->isNotEmpty())
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">High Risk Documents</h3>
            <p class="mt-1 text-sm text-gray-500">Documents with suspicious access patterns</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Level</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Factors</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($highRiskJobs as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($item['job']->original_filename, 40) }}</div>
                                <div class="text-sm text-gray-500">{{ $item['job']->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $item['risk']['level'] === 'high' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($item['risk']['level']) }} ({{ $item['risk']['score'] }})
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <ul class="text-sm text-gray-500 list-disc list-inside">
                                    @foreach($item['risk']['factors'] as $factor)
                                        <li>{{ $factor }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('admin.audit.job', $item['job']) }}" class="text-indigo-600 hover:text-indigo-900">View Details</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Suspicious Patterns --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Suspicious Patterns (Last 48 Hours)</h3>
        </div>
        <div class="p-6 space-y-6">
            {{-- Multi-Job IPs --}}
            @if($suspiciousPatterns['multi_job_ips']->isNotEmpty())
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">IPs Accessing Multiple Jobs</h4>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <ul class="space-y-2">
                        @foreach($suspiciousPatterns['multi_job_ips'] as $pattern)
                            <li class="text-sm">
                                <span class="font-mono text-yellow-800">{{ $pattern->ip_address }}</span>
                                <span class="text-yellow-600">- Accessed {{ $pattern->job_count }} different jobs</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- Rapid Downloads --}}
            @if($suspiciousPatterns['rapid_downloads']->isNotEmpty())
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">Rapid Download Patterns</h4>
                <div class="bg-red-50 rounded-lg p-4">
                    <ul class="space-y-2">
                        @foreach($suspiciousPatterns['rapid_downloads'] as $pattern)
                            <li class="text-sm">
                                <span class="font-mono text-red-800">{{ $pattern->ip_address }}</span>
                                <span class="text-red-600">- {{ $pattern->download_count }} downloads in rapid succession</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            @if($suspiciousPatterns['multi_job_ips']->isEmpty() && $suspiciousPatterns['rapid_downloads']->isEmpty())
            <div class="text-center text-gray-500 py-8">
                <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="mt-2">No suspicious patterns detected in the last 48 hours</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
