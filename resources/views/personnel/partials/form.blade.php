{{-- Reusable Personnel Form Fields --}}
{{-- Include with: $submitLabel (string), $cancelUrl (string|null) --}}
{{-- If $cancelUrl is null, renders a modal-close cancel button instead --}}

@php
    $pType = old('personnel_type', $personnel->personnel_type ?? '');

    $pnpRanks = [
        'Patrolman/Patrolwoman (PAT)',
        'Police Corporal (PCPL)',
        'Police Staff Sergeant (PSSG)',
        'Police Master Sergeant (PMSG)',
        'Police Senior Master Sergeant (PSMS)',
        'Police Chief Master Sergeant (PCMS)',
        'Police Executive Master Sergeant (PEMS)',
        'Police Lieutenant (PLT)',
        'Police Captain (PCPT)',
        'Police Major (PMAJ)',
        'Police Lieutenant Colonel (PLTCOL)',
        'Police Colonel (PCOL)',
        'Police Brigadier General (PBGEN)',
        'Police Major General (PMGEN)',
        'Police Lieutenant General (PLTGEN)',
        'Police General (PGEN)',
    ];

    $districtStations = config('stations');
@endphp

<div x-data="{
        personnelType: '{{ $pType }}',
        rank: '{{ old('rank', $personnel->rank ?? '') }}',
        badge_number: '{{ old('badge_number', $personnel->badge_number ?? '') }}',
        position_title: '{{ old('position_title', $personnel->position_title ?? '') }}',
        last_name: '{{ old('last_name', $personnel->last_name ?? '') }}',
        first_name: '{{ old('first_name', $personnel->first_name ?? '') }}',
        gender: '{{ old('gender', $personnel->gender ?? '') }}',
        age: '{{ old('age', $personnel->age ?? '') }}',
        unit: '{{ old('unit', $personnel->unit ?? '') }}',
        station: '{{ old('station', $personnel->station ?? '') }}',
        get formValid() {
            const common = this.personnelType && this.last_name.trim() && this.first_name.trim() && this.gender && this.age && this.unit.trim() && this.station;
            if (this.personnelType === 'Uniformed') return common && this.rank && this.badge_number.trim();
            if (this.personnelType === 'Non-Uniformed') return common && this.position_title.trim();
            return false;
        }
    }">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

        {{-- Personnel Type (FIRST, full width) --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Personnel Type <span class="text-red-500">*</span></label>
            <select name="personnel_type" x-model="personnelType"
                    class="w-full px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('personnel_type') border-red-400 @enderror">
                <option value="">Select type</option>
                <option value="Uniformed" {{ $pType == 'Uniformed' ? 'selected' : '' }}>Uniformed Personnel</option>
                <option value="Non-Uniformed" {{ $pType == 'Non-Uniformed' ? 'selected' : '' }}>Non-Uniformed Personnel</option>
            </select>
            @error('personnel_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Rank (Uniformed only) --}}
        <div x-show="personnelType === 'Uniformed'" x-transition>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rank <span class="text-red-500">*</span></label>
            <select name="rank" x-model="rank"
                    class="w-full px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('rank') border-red-400 @enderror"
                    :disabled="personnelType !== 'Uniformed'">
                <option value="">Select rank</option>
                @foreach($pnpRanks as $rank)
                    <option value="{{ $rank }}" {{ old('rank', $personnel->rank ?? '') == $rank ? 'selected' : '' }}>{{ $rank }}</option>
                @endforeach
            </select>
            @error('rank') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Badge Number (Uniformed only) --}}
        <div x-show="personnelType === 'Uniformed'" x-transition>
            <label class="block text-sm font-medium text-gray-700 mb-1">Badge Number <span class="text-red-500">*</span></label>
            <input type="text" name="badge_number" x-model="badge_number" value="{{ old('badge_number', $personnel->badge_number ?? '') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 @error('badge_number') border-red-400 @enderror"
                   placeholder="e.g. 12345"
                   :disabled="personnelType !== 'Uniformed'">
            @error('badge_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Position Title (NUP only) --}}
        <div x-show="personnelType === 'Non-Uniformed'" x-transition class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Position Title <span class="text-red-500">*</span></label>
            <input type="text" name="position_title" x-model="position_title" value="{{ old('position_title', $personnel->position_title ?? '') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('position_title') border-red-400 @enderror"
                   placeholder="e.g. Administrative Aide III, Records Officer"
                   :disabled="personnelType !== 'Non-Uniformed'">
            @error('position_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Last Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
            <input type="text" name="last_name" x-model="last_name" value="{{ old('last_name', $personnel->last_name ?? '') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('last_name') border-red-400 @enderror"
                   placeholder="Last name">
            @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- First Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
            <input type="text" name="first_name" x-model="first_name" value="{{ old('first_name', $personnel->first_name ?? '') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('first_name') border-red-400 @enderror"
                   placeholder="First name">
            @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Middle Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
            <input type="text" name="middle_name" value="{{ old('middle_name', $personnel->middle_name ?? '') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Middle name (optional)">
        </div>

        {{-- Gender --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
            <select name="gender" x-model="gender"
                    class="w-full px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('gender') border-red-400 @enderror">
                <option value="">Select gender</option>
                <option value="Male" {{ old('gender', $personnel->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender', $personnel->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
            </select>
            @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Age --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Age <span class="text-red-500">*</span></label>
            <input type="number" name="age" x-model="age" value="{{ old('age', $personnel->age ?? '') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('age') border-red-400 @enderror"
                   placeholder="e.g. 35" min="1" max="100">
            @error('age') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Unit --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Unit <span class="text-red-500">*</span></label>
            <input type="text" name="unit" x-model="unit" value="{{ old('unit', $personnel->unit ?? '') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('unit') border-red-400 @enderror"
                   placeholder="e.g. Intelligence Unit">
            @error('unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Station --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Station <span class="text-red-500">*</span></label>
            <select name="station" x-model="station"
                    class="w-full px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('station') border-red-400 @enderror">
                <option value="">Select station</option>
                @foreach($districtStations as $district => $stations)
                    <optgroup label="{{ $district }}">
                        @foreach($stations as $station)
                            <option value="{{ $station }}" {{ old('station', $personnel->station ?? '') == $station ? 'selected' : '' }}>{{ $station }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @error('station') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

    </div>

    {{-- Form Buttons --}}
    <div class="flex items-center justify-center gap-3 mt-6 pt-5 border-t border-gray-100">
        <button type="submit"
                :disabled="!formValid"
                :class="formValid ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                class="px-5 py-2 text-sm font-medium rounded-lg transition-colors">
            {{ $submitLabel ?? 'Save Personnel' }}
        </button>
        @if(isset($cancelUrl))
            <a href="{{ $cancelUrl }}"
               class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                Cancel
            </a>
        @else
            <button type="button" @click="showModal = false"
                    class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                Cancel
            </button>
        @endif
    </div>

</div>
