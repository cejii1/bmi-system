<x-app-layout>
    <x-slot name="pageTitle">Archived Personnel</x-slot>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Archived Personnel</h2>
            <p class="text-sm text-gray-500 mt-0.5">Archived records can be restored at any time</p>
        </div>
        <a href="{{ route('personnel.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Personnel
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-sm px-4 py-3 mb-5">
        <form method="GET" action="{{ route('personnel.archived') }}" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search archived personnel..."
                   class="flex-1 min-w-48 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit"
                    class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('personnel.archived') }}"
                   class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Rank / Title</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Station</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Archived On</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($personnel as $person)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3 text-gray-400">{{ $loop->iteration + ($personnel->currentPage() - 1) * $personnel->perPage() }}</td>
                            <td class="px-5 py-3">
                                <div class="font-medium text-gray-800">
                                    {{ $person->last_name }}, {{ $person->first_name }}
                                    {{ $person->middle_name ? strtoupper(substr($person->middle_name, 0, 1)) . '.' : '' }}
                                </div>
                                @if($person->qualification)
                                    <div class="text-xs text-gray-400">{{ $person->qualification }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $person->rank ?: $person->position_title ?: '—' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $person->station }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $person->deleted_at->format('M d, Y') }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('personnel.restore', $person->id) }}"
                                          x-data
                                          x-on:submit.prevent="$dispatch('confirm-action', { title: 'Restore Personnel', message: 'Restore {{ $person->first_name }} {{ $person->last_name }} back to active records?', type: 'info', confirmText: 'Restore', form: $el })">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 text-xs font-medium rounded-lg transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                            </svg>
                                            Restore
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('personnel.force-delete', $person->id) }}"
                                          x-data
                                          x-on:submit.prevent="$dispatch('confirm-action', { title: 'Delete Permanently', message: 'Permanently delete {{ $person->first_name }} {{ $person->last_name }}? This will also remove their user account and all BMI records. This cannot be undone.', type: 'danger', confirmText: 'Delete', form: $el })">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium rounded-lg transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                                <p class="font-medium">No archived personnel</p>
                                <p class="text-sm mt-1">Archived records will appear here</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($personnel->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $personnel->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
