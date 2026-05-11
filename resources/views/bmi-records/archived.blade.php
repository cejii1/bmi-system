<x-app-layout>
    <x-slot name="pageTitle">Archived BMI Records</x-slot>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Archived BMI Records</h2>
            <p class="text-sm text-gray-500 mt-0.5">Archived assessments can be restored at any time</p>
        </div>
        <a href="{{ route('bmi-records.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to BMI Records
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-sm px-4 py-3 mb-5">
        <form method="GET" action="{{ route('bmi-records.archived') }}" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name or badge..."
                   class="flex-1 min-w-48 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit"
                    class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('bmi-records.archived') }}"
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
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">BMI</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Archived On</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($records as $record)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3 text-gray-400">{{ $loop->iteration + ($records->currentPage() - 1) * $records->perPage() }}</td>
                            <td class="px-5 py-3">
                                <div class="font-medium text-gray-800">
                                    {{ $record->personnel->rank ?? $record->personnel->position_title }}
                                    {{ $record->personnel->last_name }},
                                    {{ $record->personnel->first_name }}
                                </div>
                                <div class="text-xs text-gray-400">{{ $record->personnel->station }}</div>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $record->assessed_date->format('M d, Y') }}</td>
                            <td class="px-5 py-3 font-semibold text-gray-800">{{ number_format($record->bmi_value, 2) }}</td>
                            <td class="px-5 py-3">
                                @php $bmi = $record->bmi_value; @endphp
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    @if($bmi < 18.5) bg-blue-100 text-blue-700
                                    @elseif($bmi < 25) bg-green-100 text-green-700
                                    @elseif($bmi < 30) bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ $record->bmi_category }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ $record->deleted_at->format('M d, Y') }}</td>
                            <td class="px-5 py-3">
                                <form method="POST" action="{{ route('bmi-records.restore', $record->id) }}"
                                      x-on:submit.prevent="$dispatch('confirm-action', { title: 'Restore Record', message: 'Restore this BMI record back to active records?', type: 'info', confirmText: 'Restore', form: $el })">
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                                <p class="font-medium">No archived BMI records</p>
                                <p class="text-sm mt-1">Archived records will appear here</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
