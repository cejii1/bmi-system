<x-app-layout>
    <x-slot name="pageTitle">BMI Assessment</x-slot>

    <div class="max-w-5xl mx-auto">

        <!-- Header with Officer Info -->
        <div class="flex items-center gap-3 mb-5">
            <a href="{{ route('my-bmi.index') }}"
               class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">BMI Assessment</h2>
                <p class="text-sm text-gray-500">Record your body measurements for this month</p>
            </div>
        </div>

        @if($existingRecord)
            <!-- Already Assessed This Month -->
            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Already Assessed</h3>
                <p class="text-sm text-gray-500 mb-5">You already have a BMI assessment for <strong>{{ \Carbon\Carbon::parse($existingRecord->assessed_date)->format('F Y') }}</strong>.</p>

                <div class="inline-flex items-center gap-6 bg-gray-50 rounded-xl px-6 py-4 mb-5">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-gray-800">{{ number_format($existingRecord->bmi_value, 2) }}</p>
                        <p class="text-xs text-gray-400 mt-1">BMI Value</p>
                    </div>
                    <div class="w-px h-10 bg-gray-200"></div>
                    <div class="text-center">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                            @if($existingRecord->bmi_category === 'Underweight') bg-blue-100 text-blue-700
                            @elseif($existingRecord->bmi_category === 'Normal') bg-green-100 text-green-700
                            @elseif($existingRecord->bmi_category === 'Overweight') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700
                            @endif">
                            {{ $existingRecord->bmi_category }}
                        </span>
                        <p class="text-xs text-gray-400 mt-1">Category</p>
                    </div>
                    <div class="w-px h-10 bg-gray-200"></div>
                    <div class="text-center">
                        <p class="text-lg font-semibold text-gray-700">{{ $existingRecord->weight }} kg</p>
                        <p class="text-xs text-gray-400 mt-1">Weight</p>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-3">
                    <a href="{{ route('self-assessment.edit') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Assessment
                    </a>
                    <a href="{{ route('my-bmi.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                        View My BMI History
                    </a>
                </div>
            </div>
        @else
            <!-- Officer Info Card -->
            <div class="bg-white rounded-xl shadow-sm px-5 py-3 mb-4 flex items-center gap-4 text-sm">
                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold shrink-0">
                    {{ strtoupper(substr($personnel->first_name, 0, 1)) }}{{ strtoupper(substr($personnel->last_name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-gray-800">{{ $personnel->rank }} {{ $personnel->last_name }}, {{ $personnel->first_name }} {{ $personnel->middle_name }}</p>
                    <p class="text-xs text-gray-400">{{ $personnel->station }} &middot; Badge: {{ $personnel->badge_number }}</p>
                </div>
                <div class="ml-auto text-right">
                    <p class="text-xs text-gray-400">Assessment for</p>
                    <p class="font-semibold text-gray-700">{{ now()->format('F Y') }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('self-assessment.store') }}" id="bmiForm" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

                    <!-- Left: Measurements -->
                    <div class="lg:col-span-3 space-y-4">

                        <div class="bg-white rounded-xl shadow-sm p-5">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center shrink-0">1</span>
                                <h3 class="font-semibold text-gray-800 text-sm">Body Measurements</h3>
                            </div>
                            <p class="text-xs text-gray-400 mb-4 ml-8">Enter your measurements below</p>

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

                            <!-- Calculate Button -->
                            <div class="mt-5 pt-4 border-t border-gray-100">
                                <button type="button" id="calculateBtn" onclick="calculate()" disabled
                                        class="w-full px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-blue-600">
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

                        <!-- Body Photos -->
                        <div id="photoSection" class="hidden">
                            <div class="bg-white rounded-xl shadow-sm p-5">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center shrink-0">2</span>
                                    <h3 class="font-semibold text-gray-800 text-sm">Body Photos</h3>
                                </div>
                                <p class="text-xs text-gray-400 mb-4 ml-8">Upload or capture full body photos (optional)</p>

                                <div class="grid grid-cols-3 gap-4">
                                    @foreach(['front' => 'Front View', 'right' => 'Right Side', 'left' => 'Left Side'] as $key => $label)
                                    <div x-data="photoCapture('photo_{{ $key }}')" class="text-center">
                                        <p class="text-xs font-medium text-gray-600 mb-2">{{ $label }}</p>
                                        <div class="relative aspect-[3/4] bg-gray-100 rounded-lg overflow-hidden border-2 border-dashed border-gray-300 mb-2">
                                            <!-- Preview -->
                                            <img x-show="preview" :src="preview" class="w-full h-full object-cover" style="display:none;">
                                            <!-- Camera -->
                                            <video x-ref="video" x-show="cameraActive" class="w-full h-full object-cover" style="display:none;" autoplay playsinline></video>
                                            <canvas x-ref="canvas" class="hidden"></canvas>
                                            <!-- Placeholder -->
                                            <div x-show="!preview && !cameraActive" class="flex flex-col items-center justify-center h-full text-gray-400">
                                                <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                <span class="text-[10px]">{{ $label }}</span>
                                            </div>
                                        </div>
                                        <div class="flex gap-1 justify-center">
                                            <label class="cursor-pointer px-2 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[10px] font-medium rounded transition-colors">
                                                <svg class="w-3 h-3 inline -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                Upload
                                                <input type="file" name="photo_{{ $key }}" accept="image/*" class="hidden" @change="handleFile($event)">
                                            </label>
                                            <button type="button" x-show="!cameraActive" @click="startCamera()" class="px-2 py-1 bg-green-50 hover:bg-green-100 text-green-700 text-[10px] font-medium rounded transition-colors">
                                                <svg class="w-3 h-3 inline -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                Camera
                                            </button>
                                            <button type="button" x-show="cameraActive" @click="capture()" class="px-2 py-1 bg-red-50 hover:bg-red-100 text-red-700 text-[10px] font-medium rounded transition-colors" style="display:none;">
                                                Capture
                                            </button>
                                            <button type="button" x-show="preview || cameraActive" @click="clear()" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 text-[10px] font-medium rounded transition-colors" style="display:none;">
                                                Clear
                                            </button>
                                        </div>
                                        @error('photo_' . $key) <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Save (hidden until calculated) -->
                        <div id="saveSection" class="hidden">
                            <div class="flex items-center justify-center gap-3">
                                <button type="submit" id="submitBtn"
                                        class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Save My Assessment
                                </button>
                                <a href="{{ route('my-bmi.index') }}"
                                   class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                                    Cancel
                                </a>
                            </div>
                        </div>

                    </div>

                    <!-- Right: Results Panel -->
                    <div class="lg:col-span-2 lg:sticky lg:top-6 lg:self-start space-y-4">

                        <!-- BMI Result -->
                        <div class="bg-white rounded-xl shadow-sm p-5">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="w-6 h-6 rounded-full bg-gray-300 text-white text-xs font-bold flex items-center justify-center shrink-0" id="step2Badge">2</span>
                                <h3 class="font-semibold text-gray-800 text-sm">BMI Result</h3>
                            </div>

                            <div id="bmiPlaceholder" class="text-center py-8 text-gray-400 text-sm">
                                <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Fill in your measurements and click<br><strong>Calculate BMI</strong> to see results
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
        @endif
    </div>

    @unless($existingRecord)
    @push('scripts')
    <script>
        const heightInput = document.getElementById('height');
        const weightInput = document.getElementById('weight');
        const waistInput  = document.getElementById('waist');
        const hipInput    = document.getElementById('hip');
        const wristInput  = document.getElementById('wrist');
        const gender      = '{{ $personnel->gender }}';
        const calcBtn     = document.getElementById('calculateBtn');

        function toggleCalcBtn() {
            const hasHeight = heightInput.value && parseFloat(heightInput.value) > 0;
            const hasWeight = weightInput.value && parseFloat(weightInput.value) > 0;
            calcBtn.disabled = !(hasHeight && hasWeight);
        }

        heightInput.addEventListener('input', toggleCalcBtn);
        weightInput.addEventListener('input', toggleCalcBtn);
        toggleCalcBtn();

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
            const weight   = parseFloat(weightInput.value);
            const waist    = parseFloat(waistInput.value) || null;
            const hip      = parseFloat(hipInput.value)   || null;
            const wrist    = parseFloat(wristInput.value) || null;

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

            document.getElementById('step2Badge').classList.remove('bg-gray-300');
            document.getElementById('step2Badge').classList.add('bg-green-600');

            document.getElementById('photoSection').classList.remove('hidden');
            document.getElementById('saveSection').classList.remove('hidden');
        }


        function photoCapture(fieldName) {
            return {
                preview: null,
                cameraActive: false,
                stream: null,
                handleFile(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.stopCamera();
                        this.preview = URL.createObjectURL(file);
                    }
                },
                async startCamera() {
                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({
                            video: { facingMode: 'environment', width: { ideal: 720 }, height: { ideal: 960 } }
                        });
                        this.$refs.video.srcObject = this.stream;
                        this.cameraActive = true;
                        this.preview = null;
                    } catch (err) {
                        alert('Could not access camera. Please use the upload option instead.');
                    }
                },
                capture() {
                    const video = this.$refs.video;
                    const canvas = this.$refs.canvas;
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0);
                    canvas.toBlob((blob) => {
                        const file = new File([blob], fieldName + '.jpg', { type: 'image/jpeg' });
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        // Find the file input for this field
                        const input = this.$el.querySelector('input[type="file"]');
                        input.files = dt.files;
                        this.preview = URL.createObjectURL(blob);
                        this.stopCamera();
                    }, 'image/jpeg', 0.85);
                },
                stopCamera() {
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                        this.stream = null;
                    }
                    this.cameraActive = false;
                },
                clear() {
                    this.stopCamera();
                    this.preview = null;
                    const input = this.$el.querySelector('input[type="file"]');
                    if (input) input.value = '';
                }
            }
        }
    </script>
    @endpush
    @endunless

</x-app-layout>
