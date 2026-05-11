<x-app-layout>
    <x-slot name="pageTitle">Reports</x-slot>

    <div>

        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">BMI Reports</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    @if(!$isAdmin && $officerStation)
                        {{ $officerStation }}
                    @else
                        Generate and export BMI assessment reports
                    @endif
                </p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm px-4 py-3 mb-4">
            <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-center gap-2">

                <select name="year" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Years</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>

                <select name="month" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Months</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                    @endforeach
                </select>

                <select name="period" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Periods</option>
                    <option value="1st Semester" {{ $selectedPeriod == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                    <option value="2nd Semester" {{ $selectedPeriod == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                </select>

                <select name="category" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    <option value="Underweight" {{ $selectedCategory == 'Underweight' ? 'selected' : '' }}>Underweight</option>
                    <option value="Normal" {{ $selectedCategory == 'Normal' ? 'selected' : '' }}>Normal</option>
                    <option value="Overweight" {{ $selectedCategory == 'Overweight' ? 'selected' : '' }}>Overweight</option>
                    <option value="Obese" {{ $selectedCategory == 'Obese' ? 'selected' : '' }}>Obese</option>
                </select>

                @if($isAdmin)
                    <select name="district" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onchange="this.form.station.value=''; this.form.submit()">
                        <option value="">All Districts</option>
                        @foreach(array_keys($districtStations) as $district)
                            <option value="{{ $district }}" {{ $selectedDistrict == $district ? 'selected' : '' }}>{{ $district }}</option>
                        @endforeach
                    </select>

                    <select name="station" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Stations</option>
                        @php
                            $stationList = $selectedDistrict && isset($districtStations[$selectedDistrict])
                                ? $districtStations[$selectedDistrict]
                                : collect($districtStations)->flatten()->sort()->values()->all();
                        @endphp
                        @foreach($stationList as $station)
                            <option value="{{ $station }}" {{ $selectedStation == $station ? 'selected' : '' }}>{{ $station }}</option>
                        @endforeach
                    </select>
                @endif

                <button type="submit"
                        class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Generate
                </button>

                @if(request()->hasAny(['year', 'month', 'period', 'category', 'district', 'station']))
                    <a href="{{ route('reports.index') }}"
                       class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                        Clear
                    </a>
                @endif

                @if($isAdmin)
                    <!-- Export Dropdown (Admin) -->
                    <div x-data="{ open: false }" x-ref="wrapper"
                         @keydown.escape.window="open = false"
                         @mousedown.window="if (open && !$refs.wrapper.contains($event.target)) open = false"
                         class="relative ml-auto">
                        <button @click="open = !open" type="button"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-1 w-44 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-20"
                             style="display: none;">
                            <button type="button"
                                    @click="open = false; setTimeout(() => { $dispatch('confirm-action', { title: 'Export as PDF', message: 'Generate and download the BMI report as a PDF file?', type: 'info', confirmText: 'Export PDF', href: '{{ route('reports.export-pdf', request()->query()) }}' }) }, 150)"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors w-full">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Export as PDF
                            </button>
                            <button type="button"
                                    @click="open = false; setTimeout(() => { $dispatch('confirm-action', { title: 'Export as Excel', message: 'Generate and download the BMI report as an Excel file?', type: 'info', confirmText: 'Export Excel', href: '{{ route('reports.export-excel', request()->query()) }}' }) }, 150)"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors w-full">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                Export as Excel
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Print Report Button (Officer) -->
                    <button type="button" x-data
                            @click="$dispatch('confirm-action', { title: 'Print Report', message: 'Generate and download the BMI report as a PDF file?', type: 'info', confirmText: 'Print Report', href: '{{ route('reports.export-pdf', request()->query()) }}' })"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors ml-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print Report
                    </button>
                @endif
            </form>
        </div>

        <!-- Summary Bar -->
        <div class="bg-white rounded-xl shadow-sm px-5 py-3 mb-4 flex items-center gap-5 text-sm">
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400 uppercase tracking-wider">Total</span>
                <span class="text-xl font-bold text-gray-800">{{ $totalRecords }}</span>
            </div>
            <div class="w-px h-8 bg-gray-200"></div>
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                <span class="text-gray-600">Underweight</span>
                <span class="font-bold text-blue-600">{{ $categoryCounts['Underweight'] }}</span>
                <span class="text-xs text-gray-400">({{ $totalRecords ? number_format($categoryCounts['Underweight'] / $totalRecords * 100, 1) : 0 }}%)</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                <span class="text-gray-600">Normal</span>
                <span class="font-bold text-green-600">{{ $categoryCounts['Normal'] }}</span>
                <span class="text-xs text-gray-400">({{ $totalRecords ? number_format($categoryCounts['Normal'] / $totalRecords * 100, 1) : 0 }}%)</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>
                <span class="text-gray-600">Overweight</span>
                <span class="font-bold text-yellow-600">{{ $categoryCounts['Overweight'] }}</span>
                <span class="text-xs text-gray-400">({{ $totalRecords ? number_format($categoryCounts['Overweight'] / $totalRecords * 100, 1) : 0 }}%)</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                <span class="text-gray-600">Obese</span>
                <span class="font-bold text-red-600">{{ $categoryCounts['Obese'] }}</span>
                <span class="text-xs text-gray-400">({{ $totalRecords ? number_format($categoryCounts['Obese'] / $totalRecords * 100, 1) : 0 }}%)</span>
            </div>
        </div>

        <!-- Tables -->
        @if($isAdmin && !empty($stationSummary))
            {{-- Admin: Side-by-side layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                <!-- Station Overview (1/3) -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden lg:col-span-1">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800 text-sm">Station Overview</h3>
                    </div>
                    <div class="overflow-y-auto max-h-[32rem]">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 border-b border-gray-100 sticky top-0 z-10">
                                <tr>
                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">Station</th>
                                    <th class="text-center px-2 py-2 font-semibold text-gray-500 uppercase w-10">Tot</th>
                                    <th class="text-center px-2 py-2 font-semibold text-blue-500 uppercase w-8">UW</th>
                                    <th class="text-center px-2 py-2 font-semibold text-green-500 uppercase w-8">N</th>
                                    <th class="text-center px-2 py-2 font-semibold text-yellow-600 uppercase w-8">OW</th>
                                    <th class="text-center px-2 py-2 font-semibold text-red-500 uppercase w-8">OB</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @php $currentDistrict = ''; @endphp
                                @foreach($stationSummary as $row)
                                    @if($row['district'] !== $currentDistrict)
                                        @php $currentDistrict = $row['district']; @endphp
                                        <tr class="bg-gray-100">
                                            <td colspan="6" class="px-3 py-1.5 text-[10px] font-bold text-gray-600 uppercase tracking-wider">
                                                {{ $currentDistrict }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-3 py-1.5 text-gray-700 pl-5">{{ str_replace([' MPS', ' PS'], '', $row['station']) }}</td>
                                        <td class="px-2 py-1.5 text-center font-semibold text-gray-800">{{ $row['total'] }}</td>
                                        <td class="px-2 py-1.5 text-center text-blue-600">{{ $row['underweight'] ?: '—' }}</td>
                                        <td class="px-2 py-1.5 text-center text-green-600">{{ $row['normal'] ?: '—' }}</td>
                                        <td class="px-2 py-1.5 text-center text-yellow-600">{{ $row['overweight'] ?: '—' }}</td>
                                        <td class="px-2 py-1.5 text-center text-red-600">{{ $row['obese'] ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Personnel Details (2/3) -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden lg:col-span-2">
                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 text-sm">Personnel Details</h3>
                        <span class="text-xs text-gray-400">{{ $totalRecords }} records</span>
                    </div>
                    @if($personnelRecords->isEmpty())
                        <div class="p-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm font-medium">No records found</p>
                        </div>
                    @else
                        <div class="overflow-x-auto overflow-y-auto max-h-[32rem]">
                            <table class="w-full text-xs">
                                <thead class="bg-gray-50 border-b border-gray-100 sticky top-0 z-10">
                                    <tr>
                                        <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">#</th>
                                        <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">Name</th>
                                        <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">Rank/Title</th>
                                        <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">Station</th>
                                        <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">Date</th>
                                        <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">H/W</th>
                                        <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">BMI</th>
                                        <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">Category</th>
                                        <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">Wt. to Lose</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($personnelRecords as $record)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-3 py-2 text-gray-400">{{ $loop->iteration }}</td>
                                            <td class="px-3 py-2 font-medium text-gray-800 whitespace-nowrap">
                                                {{ $record->personnel->last_name }}, {{ $record->personnel->first_name }}
                                            </td>
                                            <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $record->personnel->rank ?: $record->personnel->position_title ?: '—' }}</td>
                                            <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $record->personnel->station }}</td>
                                            <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $record->assessed_date->format('M d, Y') }}</td>
                                            <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $record->height }} / {{ $record->weight }}</td>
                                            <td class="px-3 py-2 font-semibold text-gray-800">{{ number_format($record->bmi_value, 2) }}</td>
                                            <td class="px-3 py-2">
                                                @php $bmi = $record->bmi_value; @endphp
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                                    @if($bmi < 18.5) bg-blue-100 text-blue-700
                                                    @elseif($bmi < 25) bg-green-100 text-green-700
                                                    @elseif($bmi < 30) bg-yellow-100 text-yellow-700
                                                    @else bg-red-100 text-red-700
                                                    @endif">
                                                    {{ $record->bmi_category }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 text-gray-600">
                                                {{ $record->weight_to_lose ? number_format($record->weight_to_lose, 1) . ' kg' : '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @else
            {{-- Officer: Full-width personnel table only (no station overview) --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 text-sm">Personnel Details</h3>
                    <span class="text-xs text-gray-400">{{ $totalRecords }} records</span>
                </div>
                @if($personnelRecords->isEmpty())
                    <div class="p-12 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm font-medium">No records found</p>
                        <p class="text-xs mt-1">Try adjusting your filters to see results.</p>
                    </div>
                @else
                    <div class="overflow-x-auto overflow-y-auto max-h-[32rem]">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 border-b border-gray-100 sticky top-0 z-10">
                                <tr>
                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">#</th>
                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">Name</th>
                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">Rank/Title</th>
                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">Date</th>
                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">Height</th>
                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">Weight</th>
                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">BMI</th>
                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">Category</th>
                                    <th class="text-left px-3 py-2 font-semibold text-gray-500 uppercase">Wt. to Lose</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($personnelRecords as $record)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-3 py-2 text-gray-400">{{ $loop->iteration }}</td>
                                        <td class="px-3 py-2 font-medium text-gray-800 whitespace-nowrap">
                                            {{ $record->personnel->last_name }}, {{ $record->personnel->first_name }}
                                        </td>
                                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $record->personnel->rank ?: $record->personnel->position_title ?: '—' }}</td>
                                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $record->assessed_date->format('M d, Y') }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ $record->height }} m</td>
                                        <td class="px-3 py-2 text-gray-600">{{ $record->weight }} kg</td>
                                        <td class="px-3 py-2 font-semibold text-gray-800">{{ number_format($record->bmi_value, 2) }}</td>
                                        <td class="px-3 py-2">
                                            @php $bmi = $record->bmi_value; @endphp
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                                @if($bmi < 18.5) bg-blue-100 text-blue-700
                                                @elseif($bmi < 25) bg-green-100 text-green-700
                                                @elseif($bmi < 30) bg-yellow-100 text-yellow-700
                                                @else bg-red-100 text-red-700
                                                @endif">
                                                {{ $record->bmi_category }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-gray-600">
                                            {{ $record->weight_to_lose ? number_format($record->weight_to_lose, 1) . ' kg' : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

    </div>
</x-app-layout>
