@extends('layouts.admin')

@section('title', 'Investigation Results')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Investigation Results</h1>
            <p class="mt-1 text-sm text-gray-600">Analysis of: {{ $filename }}</p>
        </div>
        <a href="{{ route('admin.audit.leaks') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Back to Leak Detection
        </a>
    </div>

    {{-- Conclusion Summary --}}
    @if(isset($investigation['conclusion']))
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Investigation Conclusion</h3>
            </div>
            <div class="p-6">
                @php
                    $certainty = $investigation['conclusion']['certainty'] ?? 'low';
                    $certColor = match($certainty) {
                        'high' => 'green',
                        'medium' => 'yellow',
                        default => 'gray'
                    };
                @endphp
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        @if($certainty === 'high')
                            <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @elseif($certainty === 'medium')
                            <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        @else
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-lg font-medium text-gray-900">{{ $investigation['conclusion']['message'] ?? 'Analysis complete' }}</p>
                        <p class="mt-1 text-sm text-gray-500">Certainty: <span class="font-medium text-{{ $certColor }}-600">{{ ucfirst($certainty) }}</span></p>
                        @if(!empty($investigation['conclusion']['recommendation']))
                            <p class="mt-2 text-sm text-gray-600">{{ $investigation['conclusion']['recommendation'] }}</p>
                        @endif
                    </div>
                </div>

                @if(!empty($investigation['conclusion']['factors']))
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Contributing Factors</h4>
                        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                            @foreach($investigation['conclusion']['factors'] as $factor)
                                <li>{{ $factor }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Traced Recipient --}}
    @if(isset($investigation['traced_recipient']) && $investigation['traced_recipient'])
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Traced Recipient</h3>
                <p class="mt-1 text-sm text-gray-500">Document was traced to this recipient</p>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Recipient Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $investigation['traced_recipient']['name'] ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Recipient Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $investigation['traced_recipient']['email'] ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Issue Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if(isset($investigation['traced_recipient']['issued_at']))
                                {{ \Carbon\Carbon::parse($investigation['traced_recipient']['issued_at'])->format('M d, Y H:i') }}
                            @else
                                N/A
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Unique Marker</dt>
                        <dd class="mt-1 text-sm font-mono text-gray-900">{{ Str::limit($investigation['traced_recipient']['marker'] ?? 'N/A', 30) }}</dd>
                    </div>
                </dl>

                {{-- Link to full audit --}}
                @if(isset($investigation['document_analysis']['job']))
                    <div class="mt-6">
                        <a href="{{ route('admin.audit.job', $investigation['document_analysis']['job']->id) }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            View Full Audit Trail
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Access Pattern --}}
    @if(isset($investigation['access_pattern']) && $investigation['access_pattern'])
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Access Pattern Analysis</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500">Total Accesses</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $investigation['access_pattern']['total_accesses'] ?? 0 }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500">Downloads</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $investigation['access_pattern']['downloads'] ?? 0 }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500">Unique IPs</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ count($investigation['access_pattern']['unique_ips'] ?? []) }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dt class="text-sm font-medium text-gray-500">Last Access</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">
                            @if(isset($investigation['access_pattern']['last_access']))
                                {{ \Carbon\Carbon::parse($investigation['access_pattern']['last_access'])->diffForHumans() }}
                            @else
                                Never
                            @endif
                        </dd>
                    </div>
                </dl>

                {{-- Access Timeline --}}
                @if(!empty($investigation['access_pattern']['timeline']))
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Recent Activity</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                                        <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($investigation['access_pattern']['timeline'] as $entry)
                                        <tr>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                                {{ isset($entry['date']) ? \Carbon\Carbon::parse($entry['date'])->format('M d, H:i') : 'N/A' }}
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($entry['action'] ?? 'N/A') }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-mono text-gray-500">{{ $entry['ip'] ?? 'N/A' }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $entry['location'] ?? 'Unknown' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Document Analysis Details --}}
    @if(isset($investigation['document_analysis']))
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Document Analysis</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center space-x-2 mb-4">
                    @if($investigation['document_analysis']['traced'] ?? false)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Document Traced
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Document Not Traced
                        </span>
                    @endif

                    @if($investigation['document_analysis']['analysis']['modified'] ?? false)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Modified
                        </span>
                    @endif
                </div>

                {{-- Markers Found --}}
                @if(!empty($investigation['document_analysis']['markers_found']))
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Markers Detected</h4>
                        <ul class="space-y-2">
                            @foreach($investigation['document_analysis']['markers_found'] as $marker)
                                <li class="flex items-center text-sm">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800 mr-2">
                                        {{ ucfirst($marker['type'] ?? 'unknown') }}
                                    </span>
                                    <span class="font-mono text-gray-600">{{ Str::limit($marker['marker'] ?? '', 40) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No watermark markers were detected in this document.</p>
                @endif
            </div>
        </div>
    @endif

    {{-- Investigate Another --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Investigate Another Document</h3>
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
</div>
@endsection
