<x-app-layout>
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Batch Jobs</h1>
            <p class="text-gray-600 mt-1">View and manage your batch uploads.</p>
        </div>
        <a href="{{ route('batch.create') }}"
           class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Batch
        </a>
    </div>

    @if($batches->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No batch jobs yet</h3>
        <p class="text-gray-600 mb-6">Upload multiple PDFs at once to speed up your workflow.</p>
        <a href="{{ route('batch.create') }}"
           class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors">
            Create Your First Batch
        </a>
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Files</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($batches as $batch)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-medium text-gray-900">{{ $batch->name ?? 'Batch #' . $batch->id }}</p>
                            <p class="text-sm text-gray-500">{{ $batch->iso }} → {{ $batch->lender }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-gray-900">{{ $batch->watermark_jobs_count }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($batch->status === 'completed') bg-green-100 text-green-800
                            @elseif($batch->status === 'processing') bg-blue-100 text-blue-800
                            @elseif($batch->status === 'failed') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($batch->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $batch->created_at->diffForHumans() }}
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('batch.show', $batch) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                            View
                        </a>
                        @if($batch->status === 'completed')
                        <a href="{{ route('batch.download', $batch) }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
                            Download
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $batches->links() }}
    </div>
    @endif
</div>
</x-app-layout>
