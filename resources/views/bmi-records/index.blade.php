<x-app-layout>
    <x-slot name="pageTitle">BMI Records</x-slot>

    <div>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">BMI Records</h2>
                <p class="text-sm text-gray-500 mt-0.5">All BMI assessments in the system</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('bmi-records.archived') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    Archived
                </a>
                <a href="{{ route('bmi-records.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Assessment
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm px-4 py-3 mb-5">
            <form method="GET" action="{{ route('bmi-records.index') }}" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search name or badge..."
                       class="flex-1 min-w-48 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

                <select name="category" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach(['Underweight','Normal','Overweight','Obese I','Obese II'] as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>

                <select name="station" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Stations</option>
                    @foreach($stations as $station)
                        <option value="{{ $station }}" {{ request('station') == $station ? 'selected' : '' }}>{{ $station }}</option>
                    @endforeach
                </select>

                <button type="submit"
                        class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Search
                </button>

                @if(request()->hasAny(['search','category','station']))
                    <a href="{{ route('bmi-records.index') }}"
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
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Height</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Weight</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">BMI</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Wt. to Lose</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($records as $record)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3 text-gray-400">{{ $loop->iteration + ($records->currentPage() - 1) * $records->perPage() }}</td>
                                <td class="px-5 py-3">
                                    <div class="font-medium text-gray-800">
                                        {{ $record->personnel->rank ?? '' }}
                                        {{ $record->personnel->last_name ?? 'Unknown' }},
                                        {{ $record->personnel->first_name ?? '' }}
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $record->personnel->station ?? '—' }}</div>
                                </td>
                                <td class="px-5 py-3 text-gray-600">
                                    {{ $record->assessed_date->format('M d, Y') }}
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $record->height }} m</td>
                                <td class="px-5 py-3 text-gray-600">{{ $record->weight }} kg</td>
                                <td class="px-5 py-3 font-semibold text-gray-800">{{ number_format($record->bmi_value, 2) }}</td>
                                <td class="px-5 py-3">
                                    @php $bmi = $record->bmi_value; @endphp
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                        @if($bmi < 18.5) bg-blue-100 text-blue-700
                                        @elseif($bmi < 25) bg-green-100 text-green-700
                                        @elseif($bmi < 30) bg-yellow-100 text-yellow-700
                                        @elseif($bmi < 35) bg-orange-100 text-orange-700
                                        @else bg-red-100 text-red-700
                                        @endif">
                                        {{ $record->bmi_category }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-600">
                                    {{ $record->weight_to_lose ? number_format($record->weight_to_lose, 1) . ' kg' : '—' }}
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('bmi-records.show', $record) }}"
                                           class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('bmi-records.edit', $record) }}"
                                           class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('bmi-records.destroy', $record) }}"
                                              x-on:submit.prevent="$dispatch('confirm-action', { title: 'Archive Record', message: 'Archive this BMI record? You can restore it later.', type: 'warning', confirmText: 'Archive', form: $el })">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-1.5 text-gray-400 hover:text-orange-600 hover:bg-orange-50 rounded transition-colors" title="Archive">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-16 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    <p class="font-medium">No BMI records found</p>
                                    <p class="text-sm mt-1">
                                        <a href="{{ route('bmi-records.create') }}" class="text-blue-600 hover:underline">
                                            Record the first assessment
                                        </a>
                                    </p>
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
    </div>
</x-app-layout>
