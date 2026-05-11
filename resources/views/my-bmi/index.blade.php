<x-app-layout>
    <x-slot name="pageTitle">My BMI History</x-slot>

    @if(!$personnel)
        <div class="max-w-2xl mx-auto mt-12 text-center">
            <div class="bg-white rounded-xl shadow-sm p-12">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <h3 class="text-lg font-bold text-gray-800 mb-2">No Personnel Record Linked</h3>
                <p class="text-sm text-gray-500">Your account is not linked to a personnel record. Please contact an administrator.</p>
            </div>
        </div>
    @else
        <div class="max-w-5xl mx-auto">

            <!-- Profile Summary Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-5">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                    <div class="w-16 h-16 rounded-full bg-blue-600 flex items-center justify-center text-white text-2xl font-bold shrink-0">
                        {{ strtoupper(substr($personnel->first_name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xl font-bold text-gray-800">
                            @if($personnel->personnel_type === 'Uniformed')
                                {{ $personnel->rank }}
                            @endif
                            {{ $personnel->last_name }}, {{ $personnel->first_name }}
                            {{ $personnel->middle_name ?? '' }}
                        </h3>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 text-sm text-gray-500">
                            @if($personnel->personnel_type === 'Uniformed')
                                <span>Badge: <span class="font-mono font-semibold text-gray-700">{{ $personnel->badge_number }}</span></span>
                            @else
                                <span>{{ $personnel->position_title }}</span>
                            @endif
                            <span>{{ $personnel->unit }}</span>
                            <span>{{ $personnel->station }}</span>
                        </div>
                    </div>

                    <!-- Current BMI Status -->
                    @if($latestRecord)
                        <div class="bg-gray-50 rounded-xl p-4 text-center min-w-[140px]">
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Current BMI</p>
                            <p class="text-3xl font-bold
                                @if($latestRecord->bmi_value < 18.5) text-blue-600
                                @elseif($latestRecord->bmi_value < 25) text-green-600
                                @elseif($latestRecord->bmi_value < 30) text-yellow-600
                                @else text-red-600
                                @endif">
                                {{ number_format($latestRecord->bmi_value, 1) }}
                            </p>
                            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if($latestRecord->bmi_value < 18.5) bg-blue-100 text-blue-700
                                @elseif($latestRecord->bmi_value < 25) bg-green-100 text-green-700
                                @elseif($latestRecord->bmi_value < 30) bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700
                                @endif">
                                {{ $latestRecord->bmi_category }}
                            </span>
                            <p class="text-[11px] text-gray-400 mt-1.5">{{ $latestRecord->assessed_date->format('M d, Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stats Cards -->
            @if($latestRecord)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Height</p>
                        <p class="text-lg font-bold text-gray-800">{{ $latestRecord->height }} <span class="text-sm font-normal text-gray-400">m</span></p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Weight</p>
                        <p class="text-lg font-bold text-gray-800">{{ $latestRecord->weight }} <span class="text-sm font-normal text-gray-400">kg</span></p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Normal Weight Range</p>
                        <p class="text-lg font-bold text-gray-800">
                            {{ $latestRecord->normal_weight_min ? number_format($latestRecord->normal_weight_min, 1) . ' - ' . number_format($latestRecord->normal_weight_max, 1) : '—' }}
                            <span class="text-sm font-normal text-gray-400">kg</span>
                        </p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Weight to Lose</p>
                        <p class="text-lg font-bold {{ $latestRecord->weight_to_lose > 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $latestRecord->weight_to_lose ? number_format($latestRecord->weight_to_lose, 1) . ' kg' : 'None' }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Filters + History Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between px-6 py-4 border-b border-gray-100 gap-3">
                    <h3 class="font-semibold text-gray-800">Assessment History</h3>

                    <form method="GET" action="{{ route('my-bmi.index') }}" class="flex items-center gap-2">
                        @if(isset($years) && $years->isNotEmpty())
                            <select name="year" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Years</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        @endif
                        <select name="period" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Periods</option>
                            <option value="1st Semester" {{ request('period') == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                            <option value="2nd Semester" {{ request('period') == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                        </select>
                        <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Filter
                        </button>
                        @if(request()->hasAny(['year', 'period']))
                            <a href="{{ route('my-bmi.index') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                                Clear
                            </a>
                        @endif
                    </form>
                </div>

                @if($records->isEmpty())
                    <div class="p-12 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="font-medium">No BMI assessments found</p>
                        <p class="text-sm mt-1">Your BMI records will appear here once assessed.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">#</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Period</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Height</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Weight</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">BMI</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Category</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Body Frame</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Weight to Lose</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($records as $record)
                                    <tr class="hover:bg-gray-50 transition-colors {{ $loop->first && !request()->hasAny(['year', 'period']) ? 'bg-blue-50/50' : '' }}">
                                        <td class="px-5 py-3 text-gray-400">{{ $loop->iteration }}</td>
                                        <td class="px-5 py-3 text-gray-600">{{ $record->assessed_date->format('M d, Y') }}</td>
                                        <td class="px-5 py-3 text-gray-600">{{ $record->assessment_period ?? '—' }}</td>
                                        <td class="px-5 py-3 text-gray-600">{{ $record->height }} m</td>
                                        <td class="px-5 py-3 text-gray-600">{{ $record->weight }} kg</td>
                                        <td class="px-5 py-3 font-semibold text-gray-800">{{ number_format($record->bmi_value, 2) }}</td>
                                        <td class="px-5 py-3">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                                @if($record->bmi_value < 18.5) bg-blue-100 text-blue-700
                                                @elseif($record->bmi_value < 25) bg-green-100 text-green-700
                                                @elseif($record->bmi_value < 30) bg-yellow-100 text-yellow-700
                                                @else bg-red-100 text-red-700
                                                @endif">
                                                {{ $record->bmi_category }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-gray-600">{{ $record->body_frame ?? '—' }}</td>
                                        <td class="px-5 py-3 text-gray-600">
                                            {{ $record->weight_to_lose ? number_format($record->weight_to_lose, 2) . ' kg' : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    @if($records->count() >= 2)
                        @php
                            $oldest = $records->last();
                            $newest = $records->first();
                            $weightChange = $newest->weight - $oldest->weight;
                            $bmiChange = $newest->bmi_value - $oldest->bmi_value;
                        @endphp
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                            <div class="flex flex-wrap items-center gap-6 text-sm">
                                <div>
                                    <span class="text-gray-400">Total Assessments:</span>
                                    <span class="font-semibold text-gray-800 ml-1">{{ $records->count() }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400">Weight Change:</span>
                                    <span class="font-semibold ml-1 {{ $weightChange > 0 ? 'text-red-600' : ($weightChange < 0 ? 'text-green-600' : 'text-gray-800') }}">
                                        {{ $weightChange > 0 ? '+' : '' }}{{ number_format($weightChange, 1) }} kg
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-400">BMI Change:</span>
                                    <span class="font-semibold ml-1 {{ $bmiChange > 0 ? 'text-red-600' : ($bmiChange < 0 ? 'text-green-600' : 'text-gray-800') }}">
                                        {{ $bmiChange > 0 ? '+' : '' }}{{ number_format($bmiChange, 2) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-400">Period:</span>
                                    <span class="font-semibold text-gray-800 ml-1">
                                        {{ $oldest->assessed_date->format('M Y') }} — {{ $newest->assessed_date->format('M Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @endif
</x-app-layout>
