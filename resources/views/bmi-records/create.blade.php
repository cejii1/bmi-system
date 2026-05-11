@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css">
<style>
    .ts-wrapper .ts-control { border-radius: 0.5rem; border-color: #d1d5db; padding: 0.4rem 0.75rem; font-size: 0.875rem; }
    .ts-wrapper.focus .ts-control { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.3); }
</style>
@endpush

<x-app-layout>
    <x-slot name="pageTitle">New BMI Assessment</x-slot>

    <div class="max-w-6xl mx-auto">

        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('bmi-records.index') }}"
               class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">New BMI Assessment</h2>
                <p class="text-sm text-gray-500">Select an officer, enter measurements, then calculate</p>
            </div>
        </div>

        <form method="POST" action="{{ route('bmi-records.store') }}" id="bmiForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

                <!-- Left: Inputs -->
                <div class="lg:col-span-3 space-y-4">

                    <!-- Step 1: Select Personnel -->
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center shrink-0">1</span>
                            <h3 class="font-semibold text-gray-800 text-sm">Select Officer</h3>
                        </div>
                        <select name="personnel_id" id="personnel_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('personnel_id') border-red-400 @enderror">
                            <option value="">Type to search by name or badge...</option>
                            @foreach($personnelList as $p)
                                <option value="{{ $p->id }}"
                                    {{ old('personnel_id', $personnel?->id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->rank }} {{ $p->last_name }}, {{ $p->first_name }}
                                    (Badge: {{ $p->badge_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('personnel_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Step 2: Body Measurements -->
                    <div id="measurementsSection" class="bg-white rounded-xl shadow-sm p-5 transition-all duration-300 {{ old('personnel_id', $personnel?->id) ? '' : 'opacity-40 pointer-events-none' }}">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-6 h-6 rounded-full {{ old('personnel_id', $personnel?->id) ? 'bg-blue-600' : 'bg-gray-300' }} text-white text-xs font-bold flex items-center justify-center shrink-0" id="step2Badge">2</span>
                            <h3 class="font-semibold text-gray-800 text-sm">Body Measurements</h3>
                        </div>
                        <p id="measurementsHint" class="text-xs text-amber-500 mb-4 ml-8 {{ old('personnel_id', $personnel?->id) ? 'hidden' : '' }}">Select an officer first</p>
                        <p id="measurementsReady" class="text-xs text-gray-400 mb-4 ml-8 {{ old('personnel_id', $personnel?->id) ? '' : 'hidden' }}">Enter the officer's measurements below</p>

                        <div class="grid grid-cols-2 gap-x-4 gap-y-8">

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Height (cm) <span class="text-red-500">*</span></label>
                                <input type="number" name="height" id="height" value="{{ old('height') }}"
                                       step="1" min="50" max="250" placeholder="e.g. 170"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('height') border-red-400 @enderror">
                                <p class="text-[11px] text-gray-400 mt-0.5">Enter height in centimeters</p>
                                @error('height') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Weight (kg) <span class="text-red-500">*</span></label>
                                <input type="number" name="weight" id="weight" value="{{ old('weight') }}"
                                       step="0.1" min="10" max="300" placeholder="e.g. 70.5"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('weight') border-red-400 @enderror">
                                @error('weight') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Waist (cm)</label>
                                <input type="number" name="waist" id="waist" value="{{ old('waist') }}"
                                       step="0.1" min="30" max="200" placeholder="e.g. 80.0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Hip (cm)</label>
                                <input type="number" name="hip" id="hip" value="{{ old('hip') }}"
                                       step="0.1" min="30" max="200" placeholder="e.g. 95.0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Wrist (cm)</label>
                                <input type="number" name="wrist" id="wrist" value="{{ old('wrist') }}"
                                       step="0.1" min="10" max="50" placeholder="e.g. 16.5"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="text-[11px] text-gray-400 mt-0.5">For body frame</p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Assessment Date <span class="text-red-500">*</span></label>
                                <input type="date" name="assessed_date"
                                       value="{{ date('Y-m-d') }}"
                                       readonly
                                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-600 cursor-not-allowed @error('assessed_date') border-red-400 @enderror">
                                <p class="text-[11px] text-gray-400 mt-0.5">Auto-set to today</p>
                                @error('assessed_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                        </div>

                        <!-- Calculate Button inside card -->
                        <div class="mt-5 pt-4 border-t border-gray-100">
                            <button type="button" id="calculateBtn" onclick="calculate()"
                                    class="w-full px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Calculate BMI
                            </button>
                        </div>
                    </div>

                    <!-- Hidden calculated fields -->
                    <input type="hidden" name="bmi_value"         id="bmi_value_input">
                    <input type="hidden" name="bmi_category"      id="bmi_category_input">
                    <input type="hidden" name="weight_to_lose"    id="weight_to_lose_input">
                    <input type="hidden" name="normal_weight_min" id="normal_weight_min_input">
                    <input type="hidden" name="normal_weight_max" id="normal_weight_max_input">
                    <input type="hidden" name="body_frame"        id="body_frame_input">
                    <input type="hidden" name="waist_hip_ratio"   id="waist_hip_ratio_input">

                    <!-- Save (hidden until calculated) -->
                    <div id="saveSection" class="hidden">
                        <div class="flex items-center justify-center gap-3">
                            <button type="submit" id="submitBtn"
                                    class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Assessment
                            </button>
                            <a href="{{ route('bmi-records.index') }}"
                               class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                                Cancel
                            </a>
                        </div>
                    </div>

                </div>

                <!-- Right: Results Panel (sticky) -->
                <div class="lg:col-span-2 lg:sticky lg:top-6 lg:self-start space-y-4">

                    <!-- BMI Result -->
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="w-6 h-6 rounded-full bg-gray-300 text-white text-xs font-bold flex items-center justify-center shrink-0" id="step3Badge">3</span>
                            <h3 class="font-semibold text-gray-800 text-sm">BMI Result</h3>
                        </div>

                        <div id="bmiPlaceholder" class="text-center py-8 text-gray-400 text-sm">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Fill in measurements and click<br><strong>Calculate BMI</strong> to see results
                        </div>

                        <div id="bmiResult" class="hidden">
                            <div class="text-center mb-5 pb-5 border-b border-gray-100">
                                <div id="bmiValue" class="text-5xl font-bold text-gray-800">—</div>
                                <div id="bmiCategoryBadge" class="inline-block mt-2 px-4 py-1.5 rounded-full text-sm font-semibold">—</div>
                            </div>

                            <div class="space-y-0 text-sm">
                                <div class="flex justify-between py-2.5 border-b border-gray-50">
                                    <span class="text-gray-500">Normal Range</span>
                                    <span id="normalRange" class="font-semibold text-gray-800">—</span>
                                </div>
                                <div class="flex justify-between py-2.5 border-b border-gray-50">
                                    <span class="text-gray-500">Weight to Lose</span>
                                    <span id="weightToLose" class="font-semibold text-gray-800">—</span>
                                </div>
                                <div class="flex justify-between py-2.5 border-b border-gray-50">
                                    <span class="text-gray-500">Body Frame</span>
                                    <span id="bodyFrame" class="font-semibold text-gray-800">—</span>
                                </div>
                                <div class="flex justify-between py-2.5">
                                    <span class="text-gray-500">Waist-Hip Ratio</span>
                                    <span id="waistHipRatio" class="font-semibold text-gray-800">—</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BMI Scale -->
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <h3 class="font-semibold text-gray-700 mb-5 text-sm">BMI Scale Reference</h3>
                        <div class="space-y-7 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-md font-medium">Underweight</span>
                                <span class="text-gray-500">Below 18.5</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-md font-medium">Normal</span>
                                <span class="text-gray-500">18.5 – 24.9</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-md font-medium">Overweight</span>
                                <span class="text-gray-500">25.0 – 29.9</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="px-2.5 py-1 bg-orange-100 text-orange-700 rounded-md font-medium">Obese I</span>
                                <span class="text-gray-500">30.0 – 34.9</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-md font-medium">Obese II</span>
                                <span class="text-gray-500">35.0 and above</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        new TomSelect('#personnel_id', {
            placeholder: 'Type to search by name or badge...',
            allowEmptyOption: true,
        });

        const heightInput  = document.getElementById('height');
        const weightInput  = document.getElementById('weight');
        const waistInput   = document.getElementById('waist');
        const hipInput     = document.getElementById('hip');
        const wristInput   = document.getElementById('wrist');
        const personnelSel = document.getElementById('personnel_id');

        const personnelGenders = {
            @foreach($personnelList as $p)
                {{ $p->id }}: '{{ $p->gender }}',
            @endforeach
        };

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
            const waist  = parseFloat(waistInput.value) || null;
            const hip    = parseFloat(hipInput.value)   || null;
            const wrist  = parseFloat(wristInput.value) || null;
            const pid    = personnelSel.value;
            const gender = pid ? (personnelGenders[pid] || 'Male') : 'Male';

            if (!heightCm || !weight || heightCm <= 0 || weight <= 0) {
                alert('Please enter both height and weight.');
                return;
            }

            const height     = heightCm / 100;
            const bmi        = weight / (height * height);
            const bmiRounded = Math.round(bmi * 100) / 100;
            const category   = getBmiCategory(bmi);
            const minWeight  = Math.round(18.5 * height * height * 10) / 10;
            const maxWeight  = Math.round(24.9 * height * height * 10) / 10;
            const wtl        = bmi > 24.9 ? Math.round((weight - maxWeight) * 10) / 10 : 0;
            const frame      = getBodyFrame(height, wrist, gender);
            const whr        = (waist && hip) ? Math.round((waist / hip) * 100) / 100 : null;

            document.getElementById('bmiPlaceholder').classList.add('hidden');
            document.getElementById('bmiResult').classList.remove('hidden');
            document.getElementById('bmiValue').textContent = bmiRounded.toFixed(2);

            const badge = document.getElementById('bmiCategoryBadge');
            badge.textContent = category.label;
            badge.className   = `inline-block mt-2 px-4 py-1.5 rounded-full text-sm font-semibold ${category.color}`;

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

            document.getElementById('step3Badge').classList.remove('bg-gray-300');
            document.getElementById('step3Badge').classList.add('bg-green-600');

            document.getElementById('saveSection').classList.remove('hidden');
        }

        function toggleMeasurements() {
            const section = document.getElementById('measurementsSection');
            const hint    = document.getElementById('measurementsHint');
            const ready   = document.getElementById('measurementsReady');
            const badge   = document.getElementById('step2Badge');
            const hasPersonnel = !!personnelSel.value;

            if (hasPersonnel) {
                section.classList.remove('opacity-40', 'pointer-events-none');
                hint.classList.add('hidden');
                ready.classList.remove('hidden');
                badge.classList.remove('bg-gray-300');
                badge.classList.add('bg-blue-600');
            } else {
                section.classList.add('opacity-40', 'pointer-events-none');
                hint.classList.remove('hidden');
                ready.classList.add('hidden');
                badge.classList.add('bg-gray-300');
                badge.classList.remove('bg-blue-600');
            }
        }

        personnelSel.addEventListener('change', toggleMeasurements);
    </script>
    @endpush

</x-app-layout>
