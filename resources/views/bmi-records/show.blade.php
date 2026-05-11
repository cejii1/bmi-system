<x-app-layout>
    <x-slot name="pageTitle">BMI Record Detail</x-slot>

    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('bmi-records.index') }}"
                   class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">BMI Assessment Record</h2>
                    <p class="text-sm text-gray-500">{{ $bmiRecord->assessed_date->format('F d, Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('bmi-records.edit', $bmiRecord) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <form method="POST" action="{{ route('bmi-records.destroy', $bmiRecord) }}"
                      x-on:submit.prevent="$dispatch('confirm-action', { title: 'Archive Record', message: 'Archive this BMI record? You can restore it later.', type: 'warning', confirmText: 'Archive', form: $el })">
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

        <!-- Personnel Info -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white text-lg font-bold shrink-0">
                    {{ strtoupper(substr($bmiRecord->personnel->first_name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">
                        {{ $bmiRecord->personnel->rank }}
                        {{ $bmiRecord->personnel->last_name }},
                        {{ $bmiRecord->personnel->first_name }}
                        {{ $bmiRecord->personnel->middle_name }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        Badge: {{ $bmiRecord->personnel->badge_number }} &bull;
                        {{ $bmiRecord->personnel->unit }} &bull;
                        {{ $bmiRecord->personnel->station }}
                    </p>
                </div>
            </div>
        </div>

        <!-- BMI Result Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">BMI Value</p>
                    <div class="text-5xl font-bold text-gray-800">{{ number_format($bmiRecord->bmi_value, 2) }}</div>
                </div>
                @php $bmi = $bmiRecord->bmi_value; @endphp
                <span class="px-4 py-2 rounded-full text-base font-semibold
                    @if($bmi < 18.5) bg-blue-100 text-blue-700
                    @elseif($bmi < 25) bg-green-100 text-green-700
                    @elseif($bmi < 30) bg-yellow-100 text-yellow-700
                    @elseif($bmi < 35) bg-orange-100 text-orange-700
                    @else bg-red-100 text-red-700
                    @endif">
                    {{ $bmiRecord->bmi_category }}
                </span>
            </div>
        </div>

        <!-- Measurements & Calculations -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <!-- Measurements -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Measurements</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-gray-500">Height</span>
                        <span class="font-medium text-gray-800">{{ $bmiRecord->height }} m</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-gray-500">Weight</span>
                        <span class="font-medium text-gray-800">{{ $bmiRecord->weight }} kg</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-gray-500">Waist</span>
                        <span class="font-medium text-gray-800">{{ $bmiRecord->waist ? $bmiRecord->waist . ' cm' : '—' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-gray-500">Hip</span>
                        <span class="font-medium text-gray-800">{{ $bmiRecord->hip ? $bmiRecord->hip . ' cm' : '—' }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Wrist</span>
                        <span class="font-medium text-gray-800">{{ $bmiRecord->wrist ? $bmiRecord->wrist . ' cm' : '—' }}</span>
                    </div>
                </div>
            </div>

            <!-- Calculations -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Calculations</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-gray-500">Normal Weight Range</span>
                        <span class="font-medium text-gray-800">
                            {{ number_format($bmiRecord->normal_weight_min, 1) }} – {{ number_format($bmiRecord->normal_weight_max, 1) }} kg
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-gray-500">Weight to Lose</span>
                        <span class="font-medium text-gray-800">
                            {{ $bmiRecord->weight_to_lose ? number_format($bmiRecord->weight_to_lose, 1) . ' kg' : '—' }}
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-50">
                        <span class="text-gray-500">Body Frame</span>
                        <span class="font-medium text-gray-800">{{ $bmiRecord->body_frame ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Waist-Hip Ratio</span>
                        <span class="font-medium text-gray-800">
                            {{ $bmiRecord->waist_hip_ratio ? number_format($bmiRecord->waist_hip_ratio, 2) : '—' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Body Photos -->
        @if($bmiRecord->photo_front || $bmiRecord->photo_right || $bmiRecord->photo_left)
        <div class="bg-white rounded-xl shadow-sm p-6 mt-5">
            <h3 class="font-semibold text-gray-800 mb-4">Body Photos</h3>
            <div class="grid grid-cols-3 gap-4">
                @foreach(['photo_front' => 'Front View', 'photo_right' => 'Right Side', 'photo_left' => 'Left Side'] as $field => $label)
                    <div class="text-center">
                        <p class="text-xs font-medium text-gray-500 mb-2">{{ $label }}</p>
                        @if($bmiRecord->$field)
                            <div class="aspect-[3/4] rounded-lg overflow-hidden bg-gray-100">
                                <img src="{{ asset('storage/' . $bmiRecord->$field) }}" alt="{{ $label }}"
                                     class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition-opacity"
                                     onclick="window.open(this.src, '_blank')">
                            </div>
                        @else
                            <div class="aspect-[3/4] rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">
                                No photo
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- View Full Profile Link -->
        <div class="mt-5">
            <a href="{{ route('personnel.show', $bmiRecord->personnel) }}"
               class="text-sm text-blue-600 hover:underline">
                ← View full personnel profile
            </a>
        </div>
    </div>
</x-app-layout>
