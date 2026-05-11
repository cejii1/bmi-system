<x-app-layout>
    <x-slot name="pageTitle">Account Approval</x-slot>

    <div>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Account Approval</h2>
                <p class="text-sm text-gray-500 mt-0.5">Review and approve officer account registrations</p>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex gap-1 mb-5 bg-white rounded-xl shadow-sm p-1.5 w-fit">
            <a href="{{ route('account-approval.index', ['filter' => 'pending']) }}"
               class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $filter === 'pending' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                Pending
                @if($pendingCount > 0)
                    <span class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full {{ $filter === 'pending' ? 'bg-white/20 text-white' : 'bg-red-100 text-red-600' }}">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('account-approval.index', ['filter' => 'approved']) }}"
               class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $filter === 'approved' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                Approved
            </a>
            <a href="{{ route('account-approval.index', ['filter' => 'all']) }}"
               class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $filter === 'all' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                All
            </a>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-5 py-3">#</th>
                            <th class="px-5 py-3">Name</th>
                            <th class="px-5 py-3">Email</th>
                            <th class="px-5 py-3">Station</th>
                            <th class="px-5 py-3">Email Verified</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Registered</th>
                            <th class="px-5 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3 text-gray-400">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td class="px-5 py-3">
                                    <div class="font-medium text-gray-800 uppercase">
                                        {{ $user->personnel->rank ?? $user->personnel->position_title ?? '' }}
                                        {{ strtoupper($user->name) }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $user->personnel->badge_number ? 'Badge: ' . $user->personnel->badge_number : $user->personnel->personnel_type ?? '' }}
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $user->email }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $user->personnel->station ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    @if($user->hasVerifiedEmail())
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded-full">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-yellow-700 bg-yellow-50 px-2 py-1 rounded-full">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @if($user->is_approved)
                                        <span class="inline-flex items-center text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded-full">Approved</span>
                                    @else
                                        <span class="inline-flex items-center text-xs font-medium text-orange-700 bg-orange-50 px-2 py-1 rounded-full">Pending Approval</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-gray-500 text-sm">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="px-5 py-3 text-center">
                                    @if(!$user->is_approved)
                                        <div class="flex items-center justify-center gap-2">
                                            <form method="POST" action="{{ route('account-approval.approve', $user) }}"
                                                  x-data
                                                  x-on:submit.prevent="$dispatch('confirm-action', { title: 'Approve Account', message: 'Approve account for {{ $user->name }}?', type: 'info', confirmText: 'Approve', form: $el })">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 text-xs font-medium rounded-lg transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Approve
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('account-approval.reject', $user) }}"
                                                  x-data
                                                  x-on:submit.prevent="$dispatch('confirm-action', { title: 'Reject Account', message: 'Reject and remove account for {{ $user->name }}? This cannot be undone.', type: 'danger', confirmText: 'Reject', form: $el })">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium rounded-lg transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-16 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="font-medium">No {{ $filter === 'pending' ? 'pending' : '' }} accounts found</p>
                                    <p class="text-sm mt-1">{{ $filter === 'pending' ? 'All accounts have been reviewed.' : 'No officer accounts yet.' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
