<x-app-layout>
    <x-slot name="pageTitle">Edit Personnel</x-slot>

    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('personnel.show', $personnel) }}"
               class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Edit Personnel</h2>
                <p class="text-sm text-gray-500">
                    {{ $personnel->rank }} {{ $personnel->last_name }}, {{ $personnel->first_name }}
                </p>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="{{ route('personnel.update', $personnel) }}"
                  x-on:submit.prevent="$dispatch('confirm-action', { title: 'Update Personnel', message: 'Save changes to this personnel record?', type: 'info', confirmText: 'Update', form: $el })">
                @csrf
                @method('PATCH')
                @include('personnel.partials.form', ['submitLabel' => 'Update Personnel', 'cancelUrl' => route('personnel.show', $personnel)])
            </form>
        </div>
    </div>
</x-app-layout>
