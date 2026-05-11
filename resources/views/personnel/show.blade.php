<x-app-layout>
    <x-slot name="pageTitle">Personnel Profile</x-slot>

    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('personnel.index') }}"
                   class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Personnel Profile</h2>
                    <p class="text-sm text-gray-500">Viewing full record</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('personnel.edit', $personnel) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <form method="POST" action="{{ route('personnel.destroy', $personnel) }}"
                      x-on:submit.prevent="$dispatch('confirm-action', { title: 'Archive Personnel', message: 'Archive {{ $personnel->first_name }} {{ $personnel->last_name }}? You can restore them later.', type: 'warning', confirmText: 'Archive', form: $el })">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        Archive
                    </button>
                </form>
            </div>
        </div>

        <!-- Profile Card + Latest BMI Side by Side -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

            <!-- Profile Info (2/3) -->
            <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2">
                <div class="flex items-center gap-5 mb-6">
                    @if($personnel->user && $personnel->user->profile_photo)
                        <img src="{{ $personnel->user->getProfilePhotoUrl() }}" alt=""
                             class="w-20 h-20 rounded-full object-cover shrink-0 ring-4 ring-blue-50">
                    @else
                        <div class="w-20 h-20 rounded-full bg-blue-600 flex items-center justify-center text-white text-3xl font-bold shrink-0 ring-4 ring-blue-50">
                            {{ strtoupper(substr($personnel->first_name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 uppercase">
                            {{ $personnel->last_name }}, {{ $personnel->first_name }}
                            {{ $personnel->middle_name ?? '' }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-0.5">
                            {{ $personnel->personnel_type ?? 'Uniformed' }} Personnel
                        </p>
                        @if($personnel->badge_number)
                            <p class="text-sm text-gray-500 mt-0.5">
                                Badge No: <span class="font-mono font-semibold text-gray-700">{{ $personnel->badge_number }}</span>
                            </p>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @if($personnel->rank)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Rank</p>
                            <p class="font-semibold text-gray-800 text-sm">{{ $personnel->rank }}</p>
                        </div>
                    @endif
                    @if($personnel->position_title)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Position Title</p>
                            <p class="font-semibold text-gray-800 text-sm">{{ $personnel->position_title }}</p>
                        </div>
                    @endif
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Unit</p>
                        <p class="font-semibold text-gray-800 text-sm">{{ $personnel->unit }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Station</p>
                        <p class="font-semibold text-gray-800 text-sm">{{ $personnel->station }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Gender</p>
                        <p class="font-semibold text-gray-800 text-sm">{{ $personnel->gender }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Age</p>
                        <p class="font-semibold text-gray-800 text-sm">{{ $personnel->age ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Latest BMI Summary (1/3) -->
            <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-1">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Latest Assessment</h4>
                @php $latest = $personnel->bmiRecords->sortByDesc('assessed_date')->first(); @endphp
                @if($latest)
                    <div class="text-center mb-5">
                        @php $bmi = $latest->bmi_value; @endphp
                        <div class="text-5xl font-bold text-gray-800">{{ number_format($bmi, 1) }}</div>
                        <span class="inline-block mt-2 px-3 py-1 rounded-full text-sm font-semibold
                            @if($bmi < 18.5) bg-blue-100 text-blue-700
                            @elseif($bmi < 25) bg-green-100 text-green-700
                            @elseif($bmi < 30) bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700
                            @endif">
                            {{ $latest->bmi_category }}
                        </span>
                    </div>
                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Date</span>
                            <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($latest->assessed_date)->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Height</span>
                            <span class="font-medium text-gray-700">{{ $latest->height }} m</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Weight</span>
                            <span class="font-medium text-gray-700">{{ $latest->weight }} kg</span>
                        </div>
                        @if($latest->weight_to_lose)
                            <div class="flex justify-between">
                                <span class="text-gray-400">To Lose</span>
                                <span class="font-medium text-red-600">{{ number_format($latest->weight_to_lose, 1) }} kg</span>
                            </div>
                        @endif
                        @if($latest->body_frame)
                            <div class="flex justify-between">
                                <span class="text-gray-400">Body Frame</span>
                                <span class="font-medium text-gray-700">{{ $latest->body_frame }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm font-medium">No assessments yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- BMI History -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">BMI Assessment History</h3>
                <span class="text-xs text-gray-400">{{ $personnel->bmiRecords->count() }} {{ Str::plural('record', $personnel->bmiRecords->count()) }}</span>
            </div>

            @if($personnel->bmiRecords->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <p class="font-medium">No BMI records yet</p>
                    <p class="text-sm mt-1">BMI assessments will appear here once recorded.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">#</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Height</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Weight</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">BMI</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Category</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Weight to Lose</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Body Frame</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($personnel->bmiRecords->sortByDesc('assessed_date') as $record)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3 text-gray-400">{{ $loop->iteration }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ \Carbon\Carbon::parse($record->assessed_date)->format('M d, Y') }}</td>
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
                                    <td class="px-5 py-3 text-gray-600">
                                        {{ $record->weight_to_lose ? number_format($record->weight_to_lose, 1) . ' kg' : '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-gray-600">{{ $record->body_frame ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
