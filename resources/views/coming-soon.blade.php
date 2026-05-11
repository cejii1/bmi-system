<x-app-layout>
    <x-slot name="pageTitle">{{ $page }}</x-slot>

    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">{{ $page }}</h2>
        <p class="text-gray-400 text-sm">This module is coming soon. Check back after Phase 1 is complete.</p>
    </div>
</x-app-layout>
