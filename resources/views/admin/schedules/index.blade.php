<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900 leading-tight">
                    {{ __('Jadwal Pelajaran Sekolah') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Tata letak jadwal KBM terstruktur per hari dengan pembatas visual.</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.schedules.auto-generate.index') }}" class="inline-flex items-center px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-bold shadow-sm transition">
                    <svg class="w-4 h-4 me-1.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Buat Jadwal Otomatis
                </a>
                <a href="{{ route('admin.schedules.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold shadow-sm transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Manual
                </a>
            </div>
        </div>
    </x-slot>

    <div class="p-4 sm:p-6 space-y-5">
        @if(session('generator_warnings'))
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1">
                <span class="font-bold flex items-center text-amber-800">
                    ⚠️ Pemberitahuan Generator Jadwal:
                </span>
                <ul class="list-disc list-inside space-y-0.5 text-slate-700">
                    @foreach(session('generator_warnings') as $w)
                        <li>{{ $w }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- Filter Card -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
            <form method="GET" action="{{ route('admin.schedules.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-600 block mb-1">Tahun Ajaran</label>
                    <select name="academic_year_id" onchange="this.form.submit()" class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                {{ $year->name }} ({{ $year->semester == 'odd' ? 'Ganjil' : 'Genap' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-600 block mb-1">Filter Kelas</label>
                    <select name="class_id" onchange="this.form.submit()" class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} (Kelas {{ $c->grade_level }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-600 block mb-1">Filter Hari</label>
                    <select name="day_of_week" onchange="this.form.submit()" class="w-full text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <option value="">Semua Hari (Tampilan Terstruktur)</option>
                        @foreach($daysMap as $dayKey => $dMeta)
                            <option value="{{ $dayKey }}" {{ request('day_of_week') == $dayKey ? 'selected' : '' }}>
                                {{ $dMeta['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold transition flex-1 sm:flex-none">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('admin.schedules.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition text-center flex-1 sm:flex-none">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Quick Day Nav Pills -->
        <div class="flex items-center space-x-1.5 overflow-x-auto pb-1">
            <span class="text-xs font-bold text-slate-500 me-2 whitespace-nowrap">Lompat ke Hari:</span>
            <a href="{{ request()->fullUrlWithQuery(['day_of_week' => null]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ !request('day_of_week') ? 'bg-slate-800 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                Semua Hari
            </a>
            @foreach($daysMap as $dayKey => $dMeta)
                <a href="{{ request()->fullUrlWithQuery(['day_of_week' => $dayKey]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap {{ request('day_of_week') == $dayKey ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    {{ $dMeta['label'] }} ({{ isset($schedulesByDay[$dayKey]) ? $schedulesByDay[$dayKey]->count() : 0 }})
                </a>
            @endforeach
        </div>

        <!-- Schedules Grouped by Day with Clear Separators / Headers -->
        <div class="space-y-6">
            @php
                $targetDays = request('day_of_week') ? [request('day_of_week') => $daysMap[request('day_of_week')]] : $daysMap;
                $totalRendered = 0;
            @endphp

            @foreach($targetDays as $dayKey => $dayMeta)
                @php
                    $daySchedules = $schedulesByDay->get($dayKey, collect());
                    $totalRendered += $daySchedules->count();
                @endphp

                <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                    <!-- Day Header Divider Banner -->
                    <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2 border-l-4 {{ $dayMeta['border'] }}">
                        <div class="flex items-center space-x-2.5">
                            <span class="text-base font-black text-slate-900 tracking-tight flex items-center">
                                📅 HARI {{ strtoupper($dayMeta['label']) }}
                            </span>
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $dayMeta['badge'] }}">
                                {{ $daySchedules->count() }} Mata Pelajaran
                            </span>
                        </div>
                        <div class="text-xs text-slate-500 font-medium">
                            KBM Jam 07:00 s/d 14:00 WIB
                        </div>
                    </div>

                    <!-- Day Table -->
                    @if($daySchedules->isEmpty())
                        <div class="p-6 text-center text-slate-400">
                            <svg class="w-8 h-8 mx-auto mb-1.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-xs font-medium">Tidak ada jadwal KBM pada hari {{ $dayMeta['label'] }} untuk filter yang dipilih.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600 border-collapse">
                                <thead class="bg-slate-50/70 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                                    <tr>
                                        <th class="px-4 py-3">Waktu / Jam KBM</th>
                                        <th class="px-4 py-3">Kelas</th>
                                        <th class="px-4 py-3">Mata Pelajaran</th>
                                        <th class="px-4 py-3">Guru Pengampu</th>
                                        <th class="px-4 py-3">Ruang</th>
                                        <th class="px-4 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($daySchedules as $sched)
                                        <tr class="hover:bg-blue-50/40 transition">
                                            <td class="px-4 py-3 font-semibold text-slate-900 whitespace-nowrap">
                                                <div class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 border border-slate-200 text-xs font-bold text-blue-800">
                                                    <svg class="w-3.5 h-3.5 me-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ substr($sched->start_time, 0, 5) }} - {{ substr($sched->end_time, 0, 5) }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                                    {{ $sched->classSubject->schoolClass->name }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="font-bold text-slate-900">{{ $sched->classSubject->subject->name }}</div>
                                                <div class="text-[11px] text-slate-400 font-mono">{{ $sched->classSubject->subject->code }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-slate-800">{{ $sched->classSubject->teacher->user->name ?? '-' }}</div>
                                                <div class="text-[11px] text-slate-400">NIP: {{ $sched->classSubject->teacher->nip ?? '-' }}</div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="text-xs font-medium text-slate-600">
                                                    🏛️ {{ $sched->room ?? 'Kelas ' . $sched->classSubject->schoolClass->name }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                                <div class="inline-flex items-center space-x-1.5">
                                                    <a href="{{ route('admin.schedules.edit', $sched) }}"
                                                       class="px-2.5 py-1 rounded bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold border border-amber-200 transition">
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('admin.schedules.destroy', $sched) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-2.5 py-1 rounded bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold border border-red-200 transition">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endforeach

            @if($totalRendered === 0)
                <div class="p-8 text-center bg-white rounded-xl border border-slate-200 text-slate-500">
                    <p class="text-sm font-semibold">Tidak ada jadwal KBM yang ditemukan untuk filter yang dipilih.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
