<x-app-layout>
    <x-slot name="pageTitle">Personnel</x-slot>

    <div>

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Personnel Records</h2>
                <p class="text-sm text-gray-500 mt-0.5">Manage all personnel in the system</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('personnel.archived') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    Archived
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm px-4 py-3 mb-5">
            <form method="GET" action="{{ route('personnel.index') }}" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search name, badge, rank..."
                       class="flex-1 min-w-48 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

                <select name="unit" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Units</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit }}" {{ request('unit') == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                    @endforeach
                </select>

                <select name="station" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Stations</option>
                    @foreach($stations as $station)
                        <option value="{{ $station }}" {{ request('station') == $station ? 'selected' : '' }}>{{ $station }}</option>
                    @endforeach
                </select>

                <select name="gender" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Genders</option>
                    <option value="Male" {{ request('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ request('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                </select>

                <button type="submit"
                        class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Search
                </button>

                @if(request()->hasAny(['search', 'unit', 'station', 'gender']))
                    <a href="{{ route('personnel.index') }}"
                       class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Badge No.</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Rank / Title</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Station</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Gender</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Latest BMI</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($personnel as $person)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3 text-gray-400">{{ $loop->iteration + ($personnel->currentPage() - 1) * $personnel->perPage() }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($person->user && $person->user->profile_photo)
                                            <img src="{{ $person->user->getProfilePhotoUrl() }}" alt=""
                                                 class="w-8 h-8 rounded-full object-cover shrink-0">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                                {{ strtoupper(substr($person->first_name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="font-medium text-gray-800 uppercase">
                                            {{ $person->last_name }}, {{ $person->first_name }}
                                            {{ $person->middle_name ? strtoupper(substr($person->middle_name, 0, 1)) . '.' : '' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-gray-600 font-mono">{{ $person->badge_number ?: '—' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $person->rank ?: $person->position_title ?: '—' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $person->unit }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $person->station }}</td>
                                <td class="px-5 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ $person->gender === 'Male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                        {{ $person->gender }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    @if($person->latestBmiRecord)
                                        @php $bmi = $person->latestBmiRecord->bmi_value; @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                            @if($bmi < 18.5) bg-blue-100 text-blue-700
                                            @elseif($bmi < 25) bg-green-100 text-green-700
                                            @elseif($bmi < 30) bg-yellow-100 text-yellow-700
                                            @else bg-red-100 text-red-700
                                            @endif">
                                            {{ number_format($bmi, 1) }} — {{ $person->latestBmiRecord->bmi_category }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">Not assessed</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('personnel.show', $person) }}"
                                           class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('personnel.edit', $person) }}"
                                           class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('personnel.destroy', $person) }}"
                                              x-on:submit.prevent="$dispatch('confirm-action', { title: 'Archive Personnel', message: 'Archive {{ $person->first_name }} {{ $person->last_name }}? You can restore them later from the archive.', type: 'warning', confirmText: 'Archive', form: $el })">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-1.5 text-gray-400 hover:text-orange-600 hover:bg-orange-50 rounded transition-colors" title="Archive">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-16 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="font-medium">No personnel found</p>
                                    <p class="text-sm mt-1 text-gray-400">Personnel accounts are created by officers during registration.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($personnel->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $personnel->links() }}
                </div>
            @endif
        </div>



    </div>
</x-app-layout>
