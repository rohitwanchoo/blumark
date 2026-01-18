<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('admin.jobs.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Job #{{ $job->id }}</h1>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Status Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $job->getStatusBadgeClass() }}">
                    {{ ucfirst($job->status) }}
                </span>
                <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to delete this job?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                        Delete Job
                    </button>
                </form>
            </div>

            <!-- Job Details -->
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">User</dt>
                        <dd class="mt-1">
                            @if($job->user)
                            <a href="{{ route('admin.users.show', $job->user) }}" class="flex items-center text-indigo-600 hover:text-indigo-500">
                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-semibold text-sm mr-2">
                                    {{ substr($job->user->name, 0, 1) }}
                                </div>
                                {{ $job->user->name }}
                            </a>
                            @else
                            <span class="text-gray-400">Unknown</span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-gray-900">{{ $job->created_at->format('M j, Y g:i A') }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Original Filename</dt>
                        <dd class="mt-1 text-gray-900">{{ $job->original_filename }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">File Size</dt>
                        <dd class="mt-1 text-gray-900">{{ $job->getFormattedFileSize() }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Page Count</dt>
                        <dd class="mt-1 text-gray-900">{{ $job->page_count ?? 'N/A' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Watermark Type</dt>
                        <dd class="mt-1 text-gray-900 capitalize">{{ $job->getWatermarkType() }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Position</dt>
                        <dd class="mt-1 text-gray-900 capitalize">{{ $job->getPositionMode() }}</dd>
                    </div>

                    @if($job->processed_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Processed At</dt>
                        <dd class="mt-1 text-gray-900">{{ $job->processed_at->format('M j, Y g:i A') }}</dd>
                    </div>
                    @endif

                    @if($job->error_message)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Error Message</dt>
                        <dd class="mt-1 text-red-600">{{ $job->error_message }}</dd>
                    </div>
                    @endif
                </dl>

                @if($job->settings)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Watermark Settings</h3>
                    <pre class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 overflow-x-auto">{{ json_encode($job->settings, JSON_PRETTY_PRINT) }}</pre>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
