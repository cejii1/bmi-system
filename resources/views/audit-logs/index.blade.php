<x-app-layout>
    <x-slot name="pageTitle">Audit Trail</x-slot>

    <div>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Audit Trail</h2>
                <p class="text-sm text-gray-500 mt-0.5">Track all system activities and changes</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm px-4 py-3 mb-5">
            <form method="GET" action="{{ route('audit-logs.index') }}" class="flex flex-wrap items-center gap-2">
                <div class="relative flex-1 min-w-[200px] max-w-xs">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search activity..."
                           class="w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <select name="action" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $action)) }}
                        </option>
                    @endforeach
                </select>

                <select name="model" class="pl-3 pr-8 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Modules</option>
                    @foreach($models as $model)
                        <option value="{{ $model }}" {{ request('model') == $model ? 'selected' : '' }}>{{ $model }}</option>
                    @endforeach
                </select>

                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="pl-3 pr-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="From">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="pl-3 pr-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="To">

                <button type="submit"
                        class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Filter
                </button>

                @if(request()->hasAny(['search', 'action', 'model', 'user', 'date_from', 'date_to']))
                    <a href="{{ route('audit-logs.index') }}"
                       class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Activity Timeline -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            @if($logs->isEmpty())
                <div class="p-16 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="font-medium">No activity logs found</p>
                    <p class="text-sm mt-1">System activities will appear here as they occur.</p>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @php $currentDate = null; @endphp
                    @foreach($logs as $log)
                        @php $logDate = $log->created_at->format('F d, Y'); @endphp
                        @if($logDate !== $currentDate)
                            @php $currentDate = $logDate; @endphp
                            <div class="px-5 py-2 bg-gray-50 border-b border-gray-100">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $logDate }}</p>
                            </div>
                        @endif
                        <div class="px-5 py-3 hover:bg-gray-50/50 transition-colors flex items-start gap-4">
                            <!-- Icon -->
                            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 mt-0.5 {{ $log->getActionBadgeClass() }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $log->getActionIcon() !!}</svg>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-800">
                                    <span class="font-semibold">{{ $log->user_name ?? 'System' }}</span>
                                    <span class="text-gray-500">{{ $log->description }}</span>
                                </p>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $log->getActionBadgeClass() }}">
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                    @if($log->model_type)
                                        <span class="text-xs text-gray-400">{{ $log->model_type }} #{{ $log->model_id }}</span>
                                    @endif
                                    <span class="text-xs text-gray-400">{{ $log->created_at->format('g:i A') }}</span>
                                    @if($log->ip_address)
                                        <span class="text-xs text-gray-300">{{ $log->ip_address }}</span>
                                    @endif
                                </div>

                                @if($log->old_values || $log->new_values)
                                    <div x-data="{ expanded: false }" class="mt-2">
                                        <button @click="expanded = !expanded" type="button"
                                                class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                            <span x-text="expanded ? 'Hide changes' : 'View changes'"></span>
                                        </button>
                                        <div x-show="expanded" x-collapse class="mt-2 bg-gray-50 rounded-lg p-3 text-xs">
                                            @if($log->old_values && $log->new_values)
                                                <table class="w-full">
                                                    <thead>
                                                        <tr class="text-gray-500">
                                                            <th class="text-left py-1 pr-3 font-semibold">Field</th>
                                                            <th class="text-left py-1 pr-3 font-semibold">Old</th>
                                                            <th class="text-left py-1 font-semibold">New</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-gray-600">
                                                        @foreach($log->new_values as $key => $newVal)
                                                            @if(!in_array($key, ['_token', '_method', 'password', 'current_password', 'password_confirmation']))
                                                                @php $oldVal = $log->old_values[$key] ?? '—'; @endphp
                                                                @if((string) $oldVal !== (string) $newVal)
                                                                    <tr class="border-t border-gray-100">
                                                                        <td class="py-1 pr-3 font-medium text-gray-500">{{ str_replace('_', ' ', $key) }}</td>
                                                                        <td class="py-1 pr-3 text-red-600">{{ is_array($oldVal) ? json_encode($oldVal) : $oldVal }}</td>
                                                                        <td class="py-1 text-green-600">{{ is_array($newVal) ? json_encode($newVal) : $newVal }}</td>
                                                                    </tr>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($logs->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $logs->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
