<x-app-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>

    @if(auth()->user()->isAdmin())
        {{-- ADMIN DASHBOARD --}}

        <!-- Filters -->
        <form method="GET" action="{{ route('dashboard') }}" class="bg-white rounded-xl shadow-sm px-5 py-3 mb-6 flex flex-wrap items-center gap-3">
            <span class="text-sm font-medium text-gray-600">Filters:</span>
            <select name="month" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                    </option>
                @endforeach
            </select>
            <select name="year" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <select name="district" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                <option value="">All Districts</option>
                @foreach($allDistricts as $d)
                    <option value="{{ $d }}" {{ $selectedDistrict == $d ? 'selected' : '' }}>{{ $d }}</option>
                @endforeach
            </select>
            <select name="station" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                <option value="">All Stations</option>
                @foreach($allStations as $station)
                    <option value="{{ $station }}" {{ $selectedStation == $station ? 'selected' : '' }}>{{ $station }}</option>
                @endforeach
            </select>
            @if($selectedStation || $selectedDistrict)
                <a href="{{ route('dashboard', ['month' => $selectedMonth, 'year' => $selectedYear]) }}"
                   class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm rounded-lg transition-colors">Clear Filters</a>
            @endif
        </form>

        <!-- Summary + Category Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

            <!-- Assessed Summary -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-sm p-5 text-white">
                <p class="text-blue-200 text-xs font-medium uppercase tracking-wider">{{ $periodLabel }}</p>
                <p class="text-3xl font-bold mt-1">{{ $assessedCount }}</p>
                <p class="text-blue-200 text-sm mt-0.5">Assessed</p>
                <div class="mt-3 pt-3 border-t border-blue-500/30">
                    <p class="text-blue-100 text-xs">{{ $filteredPersonnel }} {{ $selectedStation ? 'in station' : ($selectedDistrict ? 'in district' : 'total') }} personnel</p>
                </div>
            </div>

            <!-- Underweight -->
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-cyan-400">
                <p class="text-xs text-gray-500 font-medium">Underweight</p>
                <p class="text-2xl font-bold text-cyan-600 mt-1">{{ $underweightCount }}</p>
                <p class="text-xs text-gray-400 mt-1">Below 18.5</p>
            </div>

            <!-- Normal -->
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-400">
                <p class="text-xs text-gray-500 font-medium">Normal</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $normalCount }}</p>
                <p class="text-xs text-gray-400 mt-1">18.5 – 24.9</p>
            </div>

            <!-- Overweight -->
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-400">
                <p class="text-xs text-gray-500 font-medium">Overweight</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $overweightCount }}</p>
                <p class="text-xs text-gray-400 mt-1">25.0 – 29.9</p>
            </div>

            <!-- Obese -->
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-400">
                <p class="text-xs text-gray-500 font-medium">Obese</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ $obeseCount }}</p>
                <p class="text-xs text-gray-400 mt-1">30.0 and above</p>
            </div>
        </div>

        <!-- Charts + Station Table Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">

            <!-- BMI Category Doughnut Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="font-semibold text-gray-800 mb-4">
                    BMI Distribution
                    @if($selectedStation)
                        <span class="text-sm font-normal text-gray-400">— {{ $selectedStation }}</span>
                    @elseif($selectedDistrict)
                        <span class="text-sm font-normal text-gray-400">— {{ $selectedDistrict }}</span>
                    @endif
                </h2>
                @if($assessedCount > 0)
                    <div class="flex items-center justify-center">
                        <div class="w-52 h-52">
                            <canvas id="bmiDoughnut"></canvas>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-5">
                        <div class="flex items-center gap-2 text-sm">
                            <span class="w-3 h-3 rounded-full bg-cyan-400 shrink-0"></span>
                            <span class="text-gray-600">Underweight ({{ $underweightCount }})</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="w-3 h-3 rounded-full bg-green-400 shrink-0"></span>
                            <span class="text-gray-600">Normal ({{ $normalCount }})</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="w-3 h-3 rounded-full bg-yellow-400 shrink-0"></span>
                            <span class="text-gray-600">Overweight ({{ $overweightCount }})</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="w-3 h-3 rounded-full bg-red-400 shrink-0"></span>
                            <span class="text-gray-600">Obese ({{ $obeseCount }})</span>
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-center h-52 text-gray-400 text-sm">
                        No data for this period
                    </div>
                @endif
            </div>

            <!-- Station Summary Table -->
            <div class="bg-white rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800">
                        Station Overview
                        @if($selectedDistrict)
                            <span class="text-sm font-normal text-gray-400">— {{ $selectedDistrict }}</span>
                        @endif
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $periodLabel }} — click a station to filter</p>
                </div>
                <div class="overflow-y-auto max-h-80">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100 sticky top-0">
                            <tr>
                                <th class="text-left px-5 py-2.5 text-xs font-semibold text-gray-500 uppercase">Station</th>
                                <th class="text-center px-5 py-2.5 text-xs font-semibold text-gray-500 uppercase">Personnel</th>
                                <th class="text-center px-5 py-2.5 text-xs font-semibold text-gray-500 uppercase">Assessed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @php $currentDistrict = ''; @endphp
                            @foreach($stationSummary as $station)
                                @if(!$selectedStation || $selectedStation == $station->station)
                                    @if(!$selectedStation && $station->district !== $currentDistrict)
                                        @php $currentDistrict = $station->district; @endphp
                                        <tr class="bg-gray-50">
                                            <td colspan="3" class="px-5 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                {{ $currentDistrict }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="hover:bg-blue-50/50 transition-colors cursor-pointer {{ $selectedStation == $station->station ? 'bg-blue-50' : '' }}"
                                        onclick="window.location='{{ route('dashboard', ['month' => $selectedMonth, 'year' => $selectedYear, 'district' => $station->district, 'station' => $station->station]) }}'">
                                        <td class="px-5 py-2.5">
                                            <span class="font-medium text-gray-800 {{ $selectedStation == $station->station ? 'text-blue-600' : '' }}">
                                                {{ $station->station }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-2.5 text-center text-gray-500">{{ $station->total }}</td>
                                        <td class="px-5 py-2.5 text-center">
                                            <span class="inline-block min-w-6 px-2 py-0.5 rounded-full text-xs font-semibold
                                                {{ $station->assessed > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                                {{ $station->assessed }}
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Charts Row 2: Monthly Trend + Station Comparison -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">

            <!-- Monthly BMI Trend (Line Chart) -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="font-semibold text-gray-800 mb-1">BMI Trend Over Time</h2>
                <p class="text-xs text-gray-400 mb-4">Last 6 months — category breakdown</p>
                @if($monthlyTrend->sum('total') > 0)
                    <div class="h-56">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                @else
                    <div class="flex items-center justify-center h-56 text-gray-400 text-sm">
                        No trend data available
                    </div>
                @endif
            </div>

            <!-- Station Comparison (Stacked Bar Chart) -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="font-semibold text-gray-800 mb-1">Station Comparison</h2>
                <p class="text-xs text-gray-400 mb-4">{{ $periodLabel }} — top stations by assessments</p>
                @if($stationComparison->count() > 0)
                    <div class="h-56">
                        <canvas id="stationComparisonChart"></canvas>
                    </div>
                @else
                    <div class="flex items-center justify-center h-56 text-gray-400 text-sm">
                        No station data for this period
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Assessments Table -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">
                    Recent BMI Assessments
                    @if($selectedStation)
                        <span class="text-sm font-normal text-gray-400">— {{ $selectedStation }}</span>
                    @elseif($selectedDistrict)
                        <span class="text-sm font-normal text-gray-400">— {{ $selectedDistrict }}</span>
                    @endif
                </h2>
                <a href="{{ route('bmi-records.index') }}"
                   class="text-sm text-blue-600 hover:underline">View all</a>
            </div>

            @if($recentRecords->isEmpty())
                <div class="p-12 text-center text-gray-400 text-sm">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    No assessments found for {{ $periodLabel }}{{ $selectedStation ? ' at ' . $selectedStation : ($selectedDistrict ? ' in ' . $selectedDistrict : '') }}.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Name</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">BMI</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Category</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Station</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentRecords as $record)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3">
                                        <a href="{{ route('bmi-records.show', $record) }}" class="font-medium text-gray-800 hover:text-blue-600">
                                            {{ $record->personnel->rank ?? $record->personnel->position_title }}
                                            {{ $record->personnel->last_name }},
                                            {{ $record->personnel->first_name }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3 text-gray-500">{{ $record->assessed_date->format('M d, Y') }}</td>
                                    <td class="px-5 py-3 font-semibold text-gray-800">{{ number_format($record->bmi_value, 2) }}</td>
                                    <td class="px-5 py-3">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                            @if($record->bmi_value < 18.5) bg-blue-100 text-blue-700
                                            @elseif($record->bmi_value < 25) bg-green-100 text-green-700
                                            @elseif($record->bmi_value < 30) bg-yellow-100 text-yellow-700
                                            @elseif($record->bmi_value < 35) bg-orange-100 text-orange-700
                                            @else bg-red-100 text-red-700
                                            @endif">
                                            {{ $record->bmi_category }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-gray-500">{{ $record->personnel->station }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    @else
        {{-- OFFICER DASHBOARD --}}

        <!-- Welcome & Profile -->
        <div class="bg-white rounded-xl shadow-sm p-5 mb-5 flex items-center gap-4">
            @if(auth()->user()->profile_photo)
                <img src="{{ auth()->user()->getProfilePhotoUrl() }}" alt="Profile"
                     class="w-14 h-14 rounded-full object-cover border-2 border-gray-200 shrink-0">
            @else
                <div class="w-14 h-14 rounded-full bg-blue-600 flex items-center justify-center text-white text-xl font-bold shrink-0">
                    {{ strtoupper(substr($personnel->first_name, 0, 1)) }}
                </div>
            @endif
            <div class="flex-1">
                <h2 class="text-lg font-bold text-gray-800">
                    {{ $personnel->rank ?? $personnel->position_title }}
                    {{ $personnel->last_name }}, {{ $personnel->first_name }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ $personnel->station }} &bull; {{ $personnel->unit }}
                    @if($personnel->badge_number) &bull; Badge: {{ $personnel->badge_number }} @endif
                </p>
            </div>
            <a href="{{ route('self-assessment.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Assessment
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-5">
            <!-- Latest BMI -->
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 {{ $latestRecord ? ($latestRecord->bmi_value < 18.5 ? 'border-cyan-400' : ($latestRecord->bmi_value < 25 ? 'border-green-400' : ($latestRecord->bmi_value < 30 ? 'border-yellow-400' : 'border-red-400'))) : 'border-gray-300' }}">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Latest BMI</p>
                <p class="text-3xl font-bold mt-1 {{ $latestRecord ? 'text-gray-800' : 'text-gray-300' }}">
                    {{ $latestRecord ? number_format($latestRecord->bmi_value, 1) : '--' }}
                </p>
            </div>

            <!-- Category -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Category</p>
                @if($latestRecord)
                    <p class="text-xl font-bold mt-1
                        @if($latestRecord->bmi_value < 18.5) text-cyan-600
                        @elseif($latestRecord->bmi_value < 25) text-green-600
                        @elseif($latestRecord->bmi_value < 30) text-yellow-600
                        @else text-red-600
                        @endif">
                        {{ $latestRecord->bmi_category }}
                    </p>
                @else
                    <p class="text-xl font-bold text-gray-300 mt-1">--</p>
                @endif
            </div>

            <!-- Last Assessment Date -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Last Assessment</p>
                <p class="text-xl font-bold mt-1 {{ $latestRecord ? 'text-gray-800' : 'text-gray-300' }}">
                    {{ $latestRecord ? $latestRecord->assessed_date->format('M d, Y') : '--' }}
                </p>
            </div>

            <!-- Total Assessments -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Assessments</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalAssessments }}</p>
            </div>
        </div>

        <!-- Body Stats & Chart Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

            <!-- Current Body Stats -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Current Body Stats</h3>
                @if($latestRecord)
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Height</p>
                            <p class="text-lg font-bold text-gray-800">{{ number_format($latestRecord->height * 100, 0) }} <span class="text-sm font-normal text-gray-400">cm</span></p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Weight</p>
                            <p class="text-lg font-bold text-gray-800">{{ number_format($latestRecord->weight, 1) }} <span class="text-sm font-normal text-gray-400">kg</span></p>
                        </div>
                        @if($latestRecord->waist)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Waist</p>
                            <p class="text-lg font-bold text-gray-800">{{ number_format($latestRecord->waist, 1) }} <span class="text-sm font-normal text-gray-400">cm</span></p>
                        </div>
                        @endif
                        @if($latestRecord->hip)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Hip</p>
                            <p class="text-lg font-bold text-gray-800">{{ number_format($latestRecord->hip, 1) }} <span class="text-sm font-normal text-gray-400">cm</span></p>
                        </div>
                        @endif
                        @if($latestRecord->weight_to_lose && $latestRecord->weight_to_lose > 0)
                        <div class="bg-red-50 rounded-lg p-3 col-span-2">
                            <p class="text-xs text-red-500">Weight to Lose</p>
                            <p class="text-lg font-bold text-red-600">{{ number_format($latestRecord->weight_to_lose, 1) }} <span class="text-sm font-normal text-red-400">kg</span></p>
                            <p class="text-xs text-gray-500 mt-1">Normal range: {{ number_format($latestRecord->normal_weight_min, 1) }} – {{ number_format($latestRecord->normal_weight_max, 1) }} kg</p>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="flex items-center justify-center h-40 text-gray-400 text-sm">
                        No assessment data yet. <a href="{{ route('self-assessment.create') }}" class="text-blue-600 hover:underline ml-1">Take your first assessment</a>
                    </div>
                @endif
            </div>

            <!-- BMI Trend Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">BMI Trend</h3>
                @if($trendRecords->count() > 1)
                    <div class="h-52">
                        <canvas id="bmiTrendChart"></canvas>
                    </div>
                @elseif($trendRecords->count() === 1)
                    <div class="flex items-center justify-center h-52 text-gray-400 text-sm">
                        Need at least 2 assessments to show trend
                    </div>
                @else
                    <div class="flex items-center justify-center h-52 text-gray-400 text-sm">
                        No assessment data yet
                    </div>
                @endif
            </div>
        </div>

        <!-- Assessment History -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Recent Assessments</h2>
                <a href="{{ route('my-bmi.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
            </div>

            @if($recentRecords->isEmpty())
                <div class="p-12 text-center text-gray-400 text-sm">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    No assessments yet. <a href="{{ route('self-assessment.create') }}" class="text-blue-600 hover:underline">Take your first assessment</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">H/W</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">BMI</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Category</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Wt. to Lose</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentRecords as $record)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3 text-gray-600">{{ $record->assessed_date->format('M d, Y') }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ number_format($record->height * 100, 0) }}cm / {{ number_format($record->weight, 1) }}kg</td>
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
                                    <td class="px-5 py-3 text-gray-500">
                                        {{ $record->weight_to_lose && $record->weight_to_lose > 0 ? number_format($record->weight_to_lose, 1) . ' kg' : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

    @if(auth()->user()->isOfficer() && isset($trendRecords) && $trendRecords->count() > 1)
    <script>
        const trendEl = document.getElementById('bmiTrendChart');
        if (trendEl) {
            new Chart(trendEl, {
                type: 'line',
                data: {
                    labels: {!! json_encode($trendRecords->map(fn($r) => $r->assessed_date->format('M Y'))) !!},
                    datasets: [{
                        label: 'BMI',
                        data: {!! json_encode($trendRecords->pluck('bmi_value')) !!},
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#3b82f6',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(ctx) {
                                    return ` BMI: ${ctx.raw.toFixed(2)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { size: 11 }, color: '#94a3b8' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#94a3b8' }
                        }
                    }
                }
            });
        }
    </script>
    @endif

    @if(auth()->user()->isAdmin())
    <script>
        const doughnutEl = document.getElementById('bmiDoughnut');
        if (doughnutEl) {
            new Chart(doughnutEl, {
                type: 'doughnut',
                data: {
                    labels: ['Underweight', 'Normal', 'Overweight', 'Obese'],
                    datasets: [{
                        data: [{{ $underweightCount }}, {{ $normalCount }}, {{ $overweightCount }}, {{ $obeseCount }}],
                        backgroundColor: ['#22d3ee', '#4ade80', '#facc15', '#f87171'],
                        borderWidth: 0,
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '65%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 10,
                            cornerRadius: 8,
                            titleFont: { size: 13 },
                            bodyFont: { size: 12 },
                            callbacks: {
                                label: function(ctx) {
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? Math.round(ctx.raw / total * 100) : 0;
                                    return ` ${ctx.raw} personnel (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Monthly Trend Line Chart
        const trendEl = document.getElementById('monthlyTrendChart');
        if (trendEl) {
            const trendData = @json($monthlyTrend);
            new Chart(trendEl, {
                type: 'line',
                data: {
                    labels: trendData.map(d => d.label),
                    datasets: [
                        {
                            label: 'Normal',
                            data: trendData.map(d => d.normal),
                            borderColor: '#4ade80',
                            backgroundColor: 'rgba(74, 222, 128, 0.1)',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            tension: 0.3,
                            fill: true,
                        },
                        {
                            label: 'Overweight',
                            data: trendData.map(d => d.overweight),
                            borderColor: '#facc15',
                            backgroundColor: 'rgba(250, 204, 21, 0.1)',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            tension: 0.3,
                            fill: true,
                        },
                        {
                            label: 'Obese',
                            data: trendData.map(d => d.obese),
                            borderColor: '#f87171',
                            backgroundColor: 'rgba(248, 113, 113, 0.1)',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            tension: 0.3,
                            fill: true,
                        },
                        {
                            label: 'Underweight',
                            data: trendData.map(d => d.underweight),
                            borderColor: '#22d3ee',
                            backgroundColor: 'rgba(34, 211, 238, 0.1)',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            tension: 0.3,
                            fill: true,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, padding: 15, font: { size: 11 } }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 10,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { size: 11 }, color: '#94a3b8', stepSize: 1 }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#94a3b8' }
                        }
                    }
                }
            });
        }

        // Station Comparison Stacked Bar Chart
        const stationEl = document.getElementById('stationComparisonChart');
        if (stationEl) {
            const stationData = @json($stationComparison);
            new Chart(stationEl, {
                type: 'bar',
                data: {
                    labels: stationData.map(d => d.station),
                    datasets: [
                        {
                            label: 'Normal',
                            data: stationData.map(d => d.normal),
                            backgroundColor: '#4ade80',
                            borderRadius: 2,
                        },
                        {
                            label: 'Overweight',
                            data: stationData.map(d => d.overweight),
                            backgroundColor: '#facc15',
                            borderRadius: 2,
                        },
                        {
                            label: 'Obese',
                            data: stationData.map(d => d.obese),
                            backgroundColor: '#f87171',
                            borderRadius: 2,
                        },
                        {
                            label: 'Underweight',
                            data: stationData.map(d => d.underweight),
                            backgroundColor: '#22d3ee',
                            borderRadius: 2,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, padding: 15, font: { size: 11 } }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 10,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: { display: false },
                            ticks: { font: { size: 10 }, color: '#94a3b8', maxRotation: 45 }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { size: 11 }, color: '#94a3b8', stepSize: 1 }
                        }
                    }
                }
            });
        }
    </script>
    @endif
    @endpush
</x-app-layout>
