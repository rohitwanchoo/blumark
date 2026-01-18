<x-app-layout>
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Shared Links</h1>
        <p class="text-gray-600 mt-1">Manage your shared document links.</p>
    </div>

    @if($links->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No shared links yet</h3>
        <p class="text-gray-600">Create shareable links from your completed watermark jobs.</p>
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Downloads</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" x-data>
                @foreach($links as $link)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900 truncate max-w-xs">{{ $link->watermarkJob->original_filename ?? 'Unknown' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($link->recipient_email)
                        <p class="text-gray-900">{{ $link->recipient_name ?? $link->recipient_email }}</p>
                        <p class="text-sm text-gray-500">{{ $link->recipient_email }}</p>
                        @else
                        <span class="text-gray-500">Anyone with link</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($link->isValid())
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Active
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Expired
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-900">
                        {{ $link->download_count }}{{ $link->max_downloads ? '/' . $link->max_downloads : '' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $link->expires_at->format('M j, Y') }}
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        @if($link->isValid())
                        <button @click="navigator.clipboard.writeText('{{ $link->getUrl() }}'); $dispatch('notify', {message: 'Link copied!'})"
                                class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                            Copy Link
                        </button>
                        @endif
                        <form action="{{ route('shares.destroy', $link) }}" method="POST" class="inline"
                              onsubmit="return confirm('Are you sure you want to revoke this link?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                Revoke
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $links->links() }}
    </div>
    @endif
</div>
</x-app-layout>
