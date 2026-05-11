<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'BMI System' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="antialiased bg-gray-50 h-screen overflow-hidden">

<div x-data="{ sidebarOpen: false }" class="flex h-screen">

    <!-- Sidebar Overlay (mobile) -->
    <div
        x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-20 bg-black/50 lg:hidden"
    ></div>

    <!-- Sidebar -->
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 text-white flex flex-col transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:relative lg:inset-auto h-screen shrink-0"
    >
        <!-- Logo -->
        <div class="flex items-center justify-center gap-3 py-4 px-4 bg-slate-800 shrink-0">
            <img src="{{ asset('images/pnp-logo.png') }}" alt="PNP" class="w-9 h-9 object-contain">
            <div class="text-center">
                <span class="text-white font-bold text-sm tracking-wide block leading-tight">BMI System</span>
                <span class="text-slate-400 text-[10px] tracking-wider uppercase">LUPPO</span>
            </div>
            <img src="{{ asset('images/luppo-logo.png') }}" alt="LUPPO" class="w-9 h-9 object-contain">
        </div>

        <!-- Nav Links -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            @if(auth()->user()->isAdmin())
                <!-- Personnel (Admin only) -->
                <a href="{{ route('personnel.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('personnel.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Personnel
                </a>

                <!-- BMI Records (Admin only) -->
                <a href="{{ route('bmi-records.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('bmi-records.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    BMI Records
                </a>

                <!-- Account Approval (Admin only) -->
                @php $pendingAccountCount = \App\Models\User::where('role', 'officer')->where('is_approved', false)->count(); @endphp
                <a href="{{ route('account-approval.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('account-approval.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Account Approval
                    @if($pendingAccountCount > 0)
                        <span class="ml-auto px-1.5 py-0.5 text-xs font-bold bg-red-500 text-white rounded-full">{{ $pendingAccountCount }}</span>
                    @endif
                </a>

                <!-- Audit Trail (Admin only) -->
                <a href="{{ route('audit-logs.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('audit-logs.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Audit Trail
                </a>
            @endif

            @if(auth()->user()->isOfficer())
                <!-- Self Assessment (Officers) -->
                <a href="{{ route('self-assessment.create') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('self-assessment.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    BMI Assessment
                </a>

                <!-- My BMI History (Officers) -->
                <a href="{{ route('my-bmi.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs('my-bmi.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    My BMI History
                </a>
            @endif

            <!-- Reports (All users) -->
            <a href="{{ route('reports.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Reports
            </a>
        </nav>

        <!-- User Info + Logout -->
        <div class="p-4 border-t border-slate-700 shrink-0">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 mb-3 p-1.5 -m-1.5 rounded-lg hover:bg-slate-700 transition-colors group">
                @if(auth()->user()->profile_photo)
                    <img src="{{ auth()->user()->getProfilePhotoUrl() }}" alt="Profile"
                         class="w-9 h-9 rounded-full object-cover border-2 border-slate-600 group-hover:border-blue-500 transition-colors shrink-0">
                @else
                    <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-bold shrink-0 border-2 border-slate-600 group-hover:border-blue-500 transition-colors">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
                <div class="min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">
                        <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-semibold tracking-wide uppercase
                            {{ auth()->user()->isAdmin() ? 'bg-yellow-500/20 text-yellow-400' : 'bg-blue-500/20 text-blue-400' }}">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                    </p>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}"
                  x-data
                  x-on:submit.prevent="$dispatch('confirm-action', { title: 'Log Out', message: 'Are you sure you want to log out?', type: 'warning', confirmText: 'Log Out', form: $el })">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Log Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 h-screen">

        <!-- Top Navbar (Fixed) -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center px-4 gap-4 shrink-0 shadow-sm z-10">
            <!-- Hamburger (mobile) -->
            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <!-- Page Title -->
            <h1 class="text-lg font-semibold text-gray-800">
                {{ $pageTitle ?? 'Dashboard' }}
            </h1>

            <!-- Right side -->
            <div class="ml-auto flex items-center gap-3">
                <span class="hidden sm:block text-sm text-gray-500">{{ now()->format('F d, Y') }}</span>
                <div class="w-px h-5 bg-gray-200 hidden sm:block"></div>
                <a href="{{ route('profile.edit') }}"
                   class="text-sm text-gray-600 hover:text-blue-600 font-medium transition-colors">
                    {{ auth()->user()->name }}
                </a>
            </div>
        </header>

        <!-- Page Content (Scrollable) -->
        <main class="flex-1 p-6 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>
</div>

<!-- Toast Notifications -->
@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed top-6 right-6 z-50 max-w-sm w-full">
        <div class="bg-white rounded-xl shadow-lg border border-green-200 p-4 flex items-start gap-3">
            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800">Success</p>
                <p class="text-sm text-gray-500 mt-0.5">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
@endif

@if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed top-6 right-6 z-50 max-w-sm w-full">
        <div class="bg-white rounded-xl shadow-lg border border-red-200 p-4 flex items-start gap-3">
            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800">Error</p>
                <p class="text-sm text-gray-500 mt-0.5">{{ session('error') }}</p>
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
@endif

<!-- Confirmation Modal -->
<div x-data="confirmModal()" x-on:confirm-action.window="open($event.detail)" x-show="showing" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" x-show="showing"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="cancel()"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm z-10 overflow-hidden" x-show="showing"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
        <div class="p-6 text-center">
            <!-- Icon -->
            <div class="mx-auto w-12 h-12 rounded-full flex items-center justify-center mb-4"
                 :class="type === 'danger' ? 'bg-red-100' : (type === 'warning' ? 'bg-orange-100' : 'bg-blue-100')">
                <svg class="w-6 h-6" :class="type === 'danger' ? 'text-red-600' : (type === 'warning' ? 'text-orange-600' : 'text-blue-600')" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <template x-if="type === 'danger' || type === 'warning'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </template>
                    <template x-if="type === 'info'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </template>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800" x-text="title"></h3>
            <p class="text-sm text-gray-500 mt-2" x-text="message"></p>
        </div>
        <div class="flex border-t border-gray-100">
            <button @click="cancel()" class="flex-1 px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button @click="confirm()" class="flex-1 px-4 py-3 text-sm font-semibold border-l border-gray-100 transition-colors"
                    :class="type === 'danger' ? 'text-red-600 hover:bg-red-50' : (type === 'warning' ? 'text-orange-600 hover:bg-orange-50' : 'text-blue-600 hover:bg-blue-50')"
                    x-text="confirmText">
            </button>
        </div>
    </div>
</div>

<script>
    function confirmModal() {
        return {
            showing: false,
            title: '',
            message: '',
            type: 'info',
            confirmText: 'Confirm',
            targetForm: null,
            targetHref: null,
            open(detail) {
                // Check HTML5 form validity first
                if (detail.form && !detail.form.checkValidity()) {
                    detail.form.reportValidity();
                    return;
                }
                this.title = detail.title || 'Are you sure?';
                this.message = detail.message || '';
                this.type = detail.type || 'info';
                this.confirmText = detail.confirmText || 'Confirm';
                this.targetForm = detail.form || null;
                this.targetHref = detail.href || null;
                this.showing = true;
            },
            confirm() {
                this.showing = false;
                if (this.targetForm) {
                    this.targetForm.submit();
                } else if (this.targetHref) {
                    window.location = this.targetHref;
                }
            },
            cancel() {
                this.showing = false;
                this.targetForm = null;
                this.targetHref = null;
            }
        }
    }

    function confirmSubmit(form, options = {}) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            window.dispatchEvent(new CustomEvent('confirm-action', {
                detail: { ...options, form: form }
            }));
        });
    }
</script>

@stack('scripts')
</body>
</html>
