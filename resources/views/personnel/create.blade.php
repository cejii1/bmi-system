<x-app-layout>
    <x-slot name="pageTitle">Add Personnel</x-slot>

    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('personnel.index') }}"
               class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Add New Personnel</h2>
                <p class="text-sm text-gray-500">Fill in the details below</p>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="{{ route('personnel.store') }}"
                  x-on:submit.prevent="$dispatch('confirm-action', { title: 'Save Personnel', message: 'Save this personnel record?', type: 'info', confirmText: 'Save', form: $el })">
                @csrf
                @include('personnel.partials.form', ['submitLabel' => 'Save Personnel', 'cancelUrl' => route('personnel.index')])
            </form>
        </div>
    </div>
</x-app-layout>
