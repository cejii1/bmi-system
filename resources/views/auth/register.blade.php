<x-guest-layout>
    <div>
        <!-- Header -->
        <div class="animate-entry delay-1">
            <h3 class="text-xl font-bold text-slate-800">Create Account</h3>
            <p class="text-slate-500 text-sm mt-1">Register to access the BMI monitoring system</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('register') }}" class="mt-4"
              x-data="{ personnelType: '{{ old('personnel_type', '') }}' }">
            @csrf

            @php
                $pnpRanks = config('stations');
                $districtStations = config('stations');

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
            @endphp

            <!-- Personnel Type -->
            <div class="animate-entry delay-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Personnel Type <span class="text-red-500">*</span></label>
                <select name="personnel_type" x-model="personnelType"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('personnel_type') border-red-400 @enderror">
                    <option value="">Select type</option>
                    <option value="Uniformed" {{ old('personnel_type') == 'Uniformed' ? 'selected' : '' }}>Uniformed Personnel</option>
                    <option value="Non-Uniformed" {{ old('personnel_type') == 'Non-Uniformed' ? 'selected' : '' }}>Non-Uniformed Personnel</option>
                </select>
                @error('personnel_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Rank (Uniformed only) -->
            <div class="mt-3 animate-entry delay-2" x-show="personnelType === 'Uniformed'" x-transition>
                <label class="block text-sm font-medium text-slate-700 mb-1">Rank <span class="text-red-500">*</span></label>
                <select name="rank"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('rank') border-red-400 @enderror"
                        :disabled="personnelType !== 'Uniformed'">
                    <option value="">Select rank</option>
                    @foreach($pnpRanks as $rank)
                        <option value="{{ $rank }}" {{ old('rank') == $rank ? 'selected' : '' }}>{{ $rank }}</option>
                    @endforeach
                </select>
                @error('rank') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Badge Number (Uniformed only) -->
            <div class="mt-3 animate-entry delay-2" x-show="personnelType === 'Uniformed'" x-transition>
                <label class="block text-sm font-medium text-slate-700 mb-1">Badge Number <span class="text-red-500">*</span></label>
                <input type="text" name="badge_number" value="{{ old('badge_number') }}"
                       class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('badge_number') border-red-400 @enderror"
                       placeholder="e.g. 12345"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                       :disabled="personnelType !== 'Uniformed'">
                @error('badge_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Position Title (NUP only) -->
            <div class="mt-3 animate-entry delay-2" x-show="personnelType === 'Non-Uniformed'" x-transition>
                <label class="block text-sm font-medium text-slate-700 mb-1">Position Title <span class="text-red-500">*</span></label>
                <input type="text" name="position_title" value="{{ old('position_title') }}"
                       class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('position_title') border-red-400 @enderror"
                       placeholder="e.g. Administrative Aide III"
                       :disabled="personnelType !== 'Non-Uniformed'">
                @error('position_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Name Fields -->
            <div class="grid grid-cols-2 gap-3 mt-3 animate-entry delay-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                           class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm uppercase focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('last_name') border-red-400 @enderror"
                           placeholder="Last name" required
                           oninput="this.value = this.value.toUpperCase()">
                    @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}"
                           class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm uppercase focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('first_name') border-red-400 @enderror"
                           placeholder="First name" required
                           oninput="this.value = this.value.toUpperCase()">
                    @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mt-3 animate-entry delay-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                           class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm uppercase focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                           placeholder="Optional"
                           oninput="this.value = this.value.toUpperCase()">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Gender <span class="text-red-500">*</span></label>
                    <select name="gender"
                            class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('gender') border-red-400 @enderror">
                        <option value="">Select</option>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mt-3 animate-entry delay-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Age <span class="text-red-500">*</span></label>
                    <input type="number" name="age" value="{{ old('age') }}"
                           class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('age') border-red-400 @enderror"
                           placeholder="e.g. 35" min="1" max="100" required>
                    @error('age') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Unit <span class="text-red-500">*</span></label>
                    <input type="text" name="unit" value="{{ old('unit') }}"
                           class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('unit') border-red-400 @enderror"
                           placeholder="e.g. Intelligence Unit" required>
                    @error('unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Station -->
            <div class="mt-3 animate-entry delay-3">
                <label class="block text-sm font-medium text-slate-700 mb-1">Station <span class="text-red-500">*</span></label>
                <select name="station"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('station') border-red-400 @enderror">
                    <option value="">Select station</option>
                    @foreach($districtStations as $district => $stations)
                        <optgroup label="{{ $district }}">
                            @foreach($stations as $station)
                                <option value="{{ $station }}" {{ old('station') == $station ? 'selected' : '' }}>{{ $station }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('station') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <hr class="my-4 border-gray-200 animate-entry delay-4">

            <!-- Account Credentials -->
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3 animate-entry delay-4">Account Credentials</p>

            <!-- Email -->
            <div class="animate-entry delay-4">
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm text-slate-900 placeholder-slate-400
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all
                              @error('email') border-red-400 @enderror"
                       placeholder="your.email@example.com" required autocomplete="username">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Password + Confirm side by side -->
            <div class="grid grid-cols-2 gap-3 mt-3 animate-entry delay-5">
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <input id="password" type="password" name="password"
                           class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm text-slate-900 placeholder-slate-400
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all
                                  @error('password') border-red-400 @enderror"
                           placeholder="Password" required autocomplete="new-password">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm text-slate-900 placeholder-slate-400
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                           placeholder="Confirm" required autocomplete="new-password">
                </div>
            </div>

            <!-- Submit -->
            <div class="mt-4 animate-entry delay-5">
                <button type="submit"
                        class="w-full py-2 px-6 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg
                               transition-all duration-200 active:scale-[0.98] flex items-center justify-center gap-2">
                    <span>Create Account</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </div>

            <!-- Login link -->
            <p class="mt-3 text-center text-sm text-slate-500 animate-entry delay-5">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                    Sign in
                </a>
            </p>
        </form>
    </div>
</x-guest-layout>
