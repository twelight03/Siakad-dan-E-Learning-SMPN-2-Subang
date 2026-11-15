<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Log Audit Aktivitas Sistem (Security Audit Trail)') }}
        </h2>
    </x-slot>

    <div class="flex-1 flex flex-col min-h-0 p-4 sm:p-6 space-y-4">
        <!-- Filter Bar -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs flex-shrink-0">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Aksi</label>
                    <select name="action" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <option value="">Semua Aksi</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>
                                {{ ucfirst($act) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Pengguna</label>
                    <select name="user_id" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->username }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Deskripsi</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Kata kunci..." class="w-full text-xs rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="flex items-center space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold uppercase transition flex-1">
                        Filter
                    </button>
                    <a href="{{ route('admin.activity-logs.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold uppercase transition text-center flex-1">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Card: Fills empty space & scrolls internally -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs flex-1 flex flex-col min-h-0 overflow-hidden">
            <div class="overflow-auto flex-1 min-h-0">
                <table class="w-full text-left text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500 sticky top-0 z-10 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 bg-slate-50 whitespace-nowrap">Waktu (WIB)</th>
                            <th class="px-4 py-3 bg-slate-50">Pengguna</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Aksi</th>
                            <th class="px-4 py-3 bg-slate-50">Entitas</th>
                            <th class="px-4 py-3 bg-slate-50">Deskripsi Aktivitas</th>
                            <th class="px-4 py-3 bg-slate-50">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50 text-xs transition-colors">
                                <td class="px-4 py-3 text-slate-600 font-mono whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-slate-900">{{ $log->user?->name ?? 'Sistem / Anonim' }}</span>
                                    @if($log->user)
                                        <span class="block text-slate-400">({{ $log->user->username }})</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $badgeClass = match($log->action) {
                                            'login' => 'bg-blue-100 text-blue-800',
                                            'create' => 'bg-green-100 text-green-800',
                                            'update' => 'bg-amber-100 text-amber-800',
                                            'delete' => 'bg-red-100 text-red-800',
                                            default => 'bg-slate-100 text-slate-800'
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full font-bold uppercase {{ $badgeClass }}">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-700 font-medium">
                                    {{ $log->entity_type ? $log->entity_type . ($log->entity_id ? " #{$log->entity_id}" : '') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-slate-800 max-w-md">
                                    {{ $log->description ?? '-' }}
                                </td>
                                <td class="px-4 py-3 font-mono text-slate-500 whitespace-nowrap">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                    Belum ada aktivitas tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->total() > 0)
                <div class="px-4 py-3 border-t border-slate-200 bg-white flex-shrink-0">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
