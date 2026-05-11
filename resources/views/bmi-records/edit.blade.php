<x-app-layout>
    <x-slot name="pageTitle">Edit BMI Record</x-slot>

    <div class="max-w-4xl mx-auto">

        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('bmi-records.show', $bmiRecord) }}"
               class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Edit BMI Record</h2>
                <p class="text-sm text-gray-500">
                    {{ $bmiRecord->personnel->rank }}
                    {{ $bmiRecord->personnel->last_name }},
                    {{ $bmiRecord->personnel->first_name }}
                    &bull; {{ $bmiRecord->assessed_date->format('M d, Y') }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('bmi-records.update', $bmiRecord) }}" id="bmiForm"
              x-on:submit.prevent="$dispatch('confirm-action', { title: 'Update Record', message: 'Save changes to this BMI record?', type: 'info', confirmText: 'Update', form: $el })">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                <!-- Left: Inputs -->
                <div class="lg:col-span-2 space-y-5">

                    <!-- Body Measurements -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Body Measurements</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Height (cm) <span class="text-red-500">*</span></label>
                                <input type="number" name="height" id="height"
                                       value="{{ old('height', round($bmiRecord->height * 100)) }}"
                                       step="1" min="50" max="250" placeholder="e.g. 170"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-400 mt-1">Enter height in centimeters</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Weight (kg) <span class="text-red-500">*</span></label>
                                <input type="number" name="weight" id="weight"
                                       value="{{ old('weight', $bmiRecord->weight) }}"
                                       step="0.1" min="10" max="300" placeholder="e.g. 70.5"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Waist (cm)</label>
                                <input type="number" name="waist" id="waist"
                                       value="{{ old('waist', $bmiRecord->waist) }}"
                                       step="0.1" min="30" max="200" placeholder="e.g. 80.0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hip (cm)</label>
                                <input type="number" name="hip" id="hip"
                                       value="{{ old('hip', $bmiRecord->hip) }}"
                                       step="0.1" min="30" max="200" placeholder="e.g. 95.0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Wrist (cm)</label>
                                <input type="number" name="wrist" id="wrist"
                                       value="{{ old('wrist', $bmiRecord->wrist) }}"
                                       step="0.1" min="10" max="50" placeholder="e.g. 16.5"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assessment Date <span class="text-red-500">*</span></label>
                                <input type="date" name="assessed_date"
                                       value="{{ old('assessed_date', $bmiRecord->assessed_date->format('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                        </div>
                    </div>

                    <!-- Calculate Button -->
                    <div>
                        <button type="button" onclick="calculate()"
                                class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Recalculate BMI
                        </button>
                    </div>

                    <!-- Hidden calculated fields -->
                    <input type="hidden" name="bmi_value"         id="bmi_value_input"         value="{{ $bmiRecord->bmi_value }}">
                    <input type="hidden" name="bmi_category"      id="bmi_category_input"      value="{{ $bmiRecord->bmi_category }}">
                    <input type="hidden" name="weight_to_lose"    id="weight_to_lose_input"    value="{{ $bmiRecord->weight_to_lose }}">
                    <input type="hidden" name="normal_weight_min" id="normal_weight_min_input" value="{{ $bmiRecord->normal_weight_min }}">
                    <input type="hidden" name="normal_weight_max" id="normal_weight_max_input" value="{{ $bmiRecord->normal_weight_max }}">
                    <input type="hidden" name="body_frame"        id="body_frame_input"        value="{{ $bmiRecord->body_frame }}">
                    <input type="hidden" name="waist_hip_ratio"   id="waist_hip_ratio_input"   value="{{ $bmiRecord->waist_hip_ratio }}">

                    <!-- Submit -->
                    <div class="flex items-center justify-center gap-3">
                        <button type="submit"
                                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Update Record
                        </button>
                        <a href="{{ route('bmi-records.show', $bmiRecord) }}"
                           class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                            Cancel
                        </a>
                    </div>
                </div>

                <!-- Right: Results -->
                <div class="space-y-4">
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <h3 class="font-semibold text-gray-800 mb-4">BMI Result</h3>
                        <div id="bmiResult">
                            <div class="text-center mb-4">
                                <div id="bmiValue" class="text-5xl font-bold text-gray-800">{{ number_format($bmiRecord->bmi_value, 2) }}</div>
                                @php $bmi = $bmiRecord->bmi_value; @endphp
                                <div id="bmiCategoryBadge" class="inline-block mt-2 px-3 py-1 rounded-full text-sm font-semibold
                                    @if($bmi < 18.5) bg-blue-100 text-blue-700
                                    @elseif($bmi < 25) bg-green-100 text-green-700
                                    @elseif($bmi < 30) bg-yellow-100 text-yellow-700
                                    @elseif($bmi < 35) bg-orange-100 text-orange-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ $bmiRecord->bmi_category }}
                                </div>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between py-2 border-b border-gray-50">
                                    <span class="text-gray-500">Normal Range</span>
                                    <span id="normalRange" class="font-medium text-gray-800">
                                        {{ number_format($bmiRecord->normal_weight_min, 1) }} – {{ number_format($bmiRecord->normal_weight_max, 1) }} kg
                                    </span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-50">
                                    <span class="text-gray-500">Weight to Lose</span>
                                    <span id="weightToLose" class="font-medium text-gray-800">
                                        {{ $bmiRecord->weight_to_lose ? number_format($bmiRecord->weight_to_lose, 1) . ' kg' : '—' }}
                                    </span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-50">
                                    <span class="text-gray-500">Body Frame</span>
                                    <span id="bodyFrame" class="font-medium text-gray-800">{{ $bmiRecord->body_frame ?? '—' }}</span>
                                </div>
                                <div class="flex justify-between py-2">
                                    <span class="text-gray-500">Waist-Hip Ratio</span>
                                    <span id="waistHipRatio" class="font-medium text-gray-800">
                                        {{ $bmiRecord->waist_hip_ratio ? number_format($bmiRecord->waist_hip_ratio, 2) : '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        const heightInput = document.getElementById('height');
        const weightInput = document.getElementById('weight');
        const waistInput  = document.getElementById('waist');
        const hipInput    = document.getElementById('hip');
        const wristInput  = document.getElementById('wrist');
        const gender      = '{{ $bmiRecord->personnel->gender }}';

        function getBmiCategory(bmi) {
            if (bmi < 18.5) return { label: 'Underweight', color: 'bg-blue-100 text-blue-700' };
            if (bmi < 25)   return { label: 'Normal',      color: 'bg-green-100 text-green-700' };
            if (bmi < 30)   return { label: 'Overweight',  color: 'bg-yellow-100 text-yellow-700' };
            if (bmi < 35)   return { label: 'Obese I',     color: 'bg-orange-100 text-orange-700' };
            return                 { label: 'Obese II',    color: 'bg-red-100 text-red-700' };
        }

        function getBodyFrame(heightM, wristCm, gender) {
            if (!wristCm || !heightM) return null;
            const r = (heightM * 100) / wristCm;
            if (gender === 'Female') {
                if (r > 10.9) return 'Small';
                if (r >= 9.9) return 'Medium';
                return 'Large';
            } else {
                if (r > 10.4) return 'Small';
                if (r >= 9.6) return 'Medium';
                return 'Large';
            }
        }

        function calculate() {
            const heightCm = parseFloat(heightInput.value);
            const weight = parseFloat(weightInput.value);
            const waist  = parseFloat(waistInput.value)  || null;
            const hip    = parseFloat(hipInput.value)    || null;
            const wrist  = parseFloat(wristInput.value)  || null;

            if (!heightCm || !weight) return;

            const height     = heightCm / 100;
            const bmi        = weight / (height * height);
            const bmiRounded = Math.round(bmi * 100) / 100;
            const category   = getBmiCategory(bmi);
            const minWeight  = Math.round(18.5 * height * height * 10) / 10;
            const maxWeight  = Math.round(24.9 * height * height * 10) / 10;
            const wtl        = bmi > 24.9 ? Math.round((weight - maxWeight) * 10) / 10 : 0;
            const frame      = getBodyFrame(height, wrist, gender);
            const whr        = (waist && hip) ? Math.round((waist / hip) * 100) / 100 : null;

            document.getElementById('bmiValue').textContent = bmiRounded.toFixed(2);
            const badge = document.getElementById('bmiCategoryBadge');
            badge.textContent = category.label;
            badge.className   = `inline-block mt-2 px-3 py-1 rounded-full text-sm font-semibold ${category.color}`;

            document.getElementById('normalRange').textContent   = `${minWeight} – ${maxWeight} kg`;
            document.getElementById('weightToLose').textContent  = wtl > 0 ? `${wtl} kg` : '—';
            document.getElementById('bodyFrame').textContent     = frame || '—';
            document.getElementById('waistHipRatio').textContent = whr ? whr.toFixed(2) : '—';

            document.getElementById('bmi_value_input').value         = bmiRounded;
            document.getElementById('bmi_category_input').value      = category.label;
            document.getElementById('weight_to_lose_input').value    = wtl > 0 ? wtl : '';
            document.getElementById('normal_weight_min_input').value = minWeight;
            document.getElementById('normal_weight_max_input').value = maxWeight;
            document.getElementById('body_frame_input').value        = frame || '';
            document.getElementById('waist_hip_ratio_input').value   = whr || '';
        }

        // Calculation only triggered by "Recalculate BMI" button
    </script>
    @endpush

</x-app-layout>
