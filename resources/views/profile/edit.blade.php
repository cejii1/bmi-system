<x-app-layout>
    <x-slot name="pageTitle">My Profile</x-slot>

    <div x-data="{ photoPreview: null, showPasswordModal: {{ $errors->updatePassword->any() ? 'true' : 'false' }} }">

        <!-- Profile Header with Photo -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-5">
            <!-- Top Row: Role badge + Change Password button -->
            <div class="flex items-center justify-between mb-5">
                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $user->isAdmin() ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                    {{ ucfirst($user->role) }}
                </span>
                <button @click="showPasswordModal = true" type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Change Password
                </button>
            </div>

            <div class="flex items-start gap-6">
                <!-- Photo Section -->
                <div class="flex flex-col items-center gap-3">
                    <div class="relative group">
                        @if($user->profile_photo)
                            <img src="{{ $user->getProfilePhotoUrl() }}" alt="Profile Photo"
                                 class="w-28 h-28 rounded-full object-cover border-4 border-white shadow-md"
                                 x-show="!photoPreview">
                        @else
                            <div class="w-28 h-28 rounded-full bg-blue-600 flex items-center justify-center text-white text-3xl font-bold border-4 border-white shadow-md"
                                 x-show="!photoPreview">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <img x-show="photoPreview" :src="photoPreview"
                             class="w-28 h-28 rounded-full object-cover border-4 border-white shadow-md" style="display:none;">
                    </div>

                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('profile.update-photo') }}" enctype="multipart/form-data" id="photoForm">
                            @csrf
                            <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-medium rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Upload
                                <input type="file" name="profile_photo" class="hidden" accept="image/*"
                                       @change="const file = $event.target.files[0]; if(file) { photoPreview = URL.createObjectURL(file); document.getElementById('photoForm').submit(); }">
                            </label>
                        </form>
                        @if($user->profile_photo)
                            <form method="POST" action="{{ route('profile.remove-photo') }}"
                                  x-data
                                  x-on:submit.prevent="$dispatch('confirm-action', { title: 'Remove Photo', message: 'Remove your profile photo?', type: 'warning', confirmText: 'Remove', form: $el })">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Remove
                                </button>
                            </form>
                        @endif
                    </div>
                    @error('profile_photo') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                    <p class="text-[11px] text-gray-400">Max 5MB. JPG, PNG, WEBP</p>
                </div>

                <!-- Info Section -->
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-gray-800">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>

                    @if($personnel)
                        <div class="grid grid-cols-2 gap-x-8 gap-y-3 mt-5 text-sm">
                            @if($personnel->rank)
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wider">Rank</p>
                                <p class="font-medium text-gray-800">{{ $personnel->rank }}</p>
                            </div>
                            @endif
                            @if($personnel->position_title)
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wider">Position</p>
                                <p class="font-medium text-gray-800">{{ $personnel->position_title }}</p>
                            </div>
                            @endif
                            @if($personnel->badge_number)
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wider">Badge Number</p>
                                <p class="font-medium text-gray-800">{{ $personnel->badge_number }}</p>
                            </div>
                            @endif
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wider">Personnel Type</p>
                                <p class="font-medium text-gray-800">{{ $personnel->personnel_type }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wider">Station</p>
                                <p class="font-medium text-gray-800">{{ $personnel->station }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wider">Unit</p>
                                <p class="font-medium text-gray-800">{{ $personnel->unit }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wider">Gender</p>
                                <p class="font-medium text-gray-800">{{ $personnel->gender }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wider">Age</p>
                                <p class="font-medium text-gray-800">{{ $personnel->age }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Update Email -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Update Email Address</h3>
            <form method="POST" action="{{ route('profile.update') }}" class="max-w-xl">
                @csrf
                @method('PATCH')
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Update Email
                </button>
            </form>
        </div>

        <!-- Change Password Modal -->
        <div x-show="showPasswordModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                 x-show="showPasswordModal"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="showPasswordModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md z-10 overflow-hidden"
                 x-show="showPasswordModal"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Change Password</h3>
                            <p class="text-xs text-gray-400">Use a strong, unique password</p>
                        </div>
                    </div>
                    <button @click="showPasswordModal = false" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="update_password_current_password" class="block text-sm font-medium text-gray-600 mb-1">Current Password</label>
                        <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @if($errors->updatePassword->has('current_password')) border-red-400 @endif">
                        @if($errors->updatePassword->has('current_password'))
                            <p class="text-red-500 text-xs mt-1">{{ $errors->updatePassword->first('current_password') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="update_password_password" class="block text-sm font-medium text-gray-600 mb-1">New Password</label>
                        <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @if($errors->updatePassword->has('password')) border-red-400 @endif">
                        @if($errors->updatePassword->has('password'))
                            <p class="text-red-500 text-xs mt-1">{{ $errors->updatePassword->first('password') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-600 mb-1">Confirm New Password</label>
                        <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="showPasswordModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
