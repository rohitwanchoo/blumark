@extends('layouts.admin')

@section('title', 'Alert Details')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.alerts.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Alert #{{ $alert->id }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $alert->created_at->format('F j, Y g:i A') }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $alert->getSeverityBadgeClass() }}">
                {{ strtoupper($alert->severity) }}
            </span>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $alert->getStatusBadgeClass() }}">
                {{ $alert->getStatusLabel() }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Alert Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $alert->title }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ $alert->getTypeLabel() }}</p>
                </div>
                <div class="px-4 py-5 sm:px-6">
                    <p class="text-gray-700">{{ $alert->description }}</p>

                    @if($alert->metadata)
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-500 mb-3">Alert Details</h4>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($alert->metadata as $key => $value)
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <dt class="text-xs font-medium text-gray-500 uppercase">{{ str_replace('_', ' ', $key) }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @if(is_array($value))
                                                {{ implode(', ', array_slice($value, 0, 5)) }}{{ count($value) > 5 ? '...' : '' }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Related Access Logs --}}
            @if($relatedLogs->count() > 0)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Related Access Logs</h3>
                        <p class="mt-1 text-sm text-gray-500">Recent access activity related to this alert</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($relatedLogs->take(20) as $log)
                                    <tr>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->created_at->diffForHumans() }}
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                {{ $log->action === 'download' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($log->action) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm font-mono text-gray-500">
                                            {{ $log->ip_address }}
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                            {{ Str::limit($log->watermarkJob?->original_filename ?? '-', 30) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Quick Info --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Information</h3>
                </div>
                <div class="px-4 py-5 sm:px-6 space-y-4">
                    @if($alert->ip_address)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $alert->ip_address }}</dd>
                        </div>
                    @endif

                    @if($alert->watermarkJob)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Document</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="{{ route('admin.audit.job', $alert->watermarkJob) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $alert->watermarkJob->original_filename }}
                                </a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Document Owner</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $alert->watermarkJob->user->name ?? 'Unknown' }}</dd>
                        </div>
                    @endif

                    @if($alert->acknowledged_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Acknowledged</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $alert->acknowledged_at->format('M j, Y g:i A') }}
                                @if($alert->acknowledgedByUser)
                                    <br><span class="text-gray-500">by {{ $alert->acknowledgedByUser->name }}</span>
                                @endif
                            </dd>
                        </div>
                    @endif

                    @if($alert->resolved_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Resolved</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $alert->resolved_at->format('M j, Y g:i A') }}
                                @if($alert->resolvedByUser)
                                    <br><span class="text-gray-500">by {{ $alert->resolvedByUser->name }}</span>
                                @endif
                            </dd>
                        </div>
                    @endif

                    @if($alert->resolution_notes)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Resolution Notes</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $alert->resolution_notes }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            @if($alert->isActionable())
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Actions</h3>
                    </div>
                    <div class="px-4 py-5 sm:px-6 space-y-3">
                        @if($alert->status === 'new')
                            <form action="{{ route('admin.alerts.acknowledge', $alert) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Acknowledge
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.alerts.resolve', $alert) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Resolution Notes</label>
                                <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Optional notes..."></textarea>
                            </div>
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Mark Resolved
                            </button>
                        </form>

                        <form action="{{ route('admin.alerts.dismiss', $alert) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50" onclick="return confirm('Dismiss this alert as a false positive?')">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Dismiss (False Positive)
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
