<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900 leading-tight flex items-center">
                    <span class="me-2 text-2xl">📅</span> {{ __('Jadwal Pelajaran SMP Negeri 2 Subang') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Struktur KBM Jam 07:00 – 14:00 WIB (Jumat s/d 11:15 WIB) • Dikelompokkan bertingkat per Tingkatan (7, 8, 9) dan per Kelas.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.schedules.settings') }}" class="inline-flex items-center px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-lg text-xs font-bold shadow-xs transition">
                    <svg class="w-4 h-4 me-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Atur Jam & Istirahat
                </a>
                <a href="{{ route('admin.schedules.auto-generate.index') }}" class="inline-flex items-center px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-bold shadow-xs transition">
                    <svg class="w-4 h-4 me-1.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Buat Jadwal Otomatis
                </a>
                <a href="{{ route('admin.schedules.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-xs transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Manual
                </a>
            </div>
        </div>
    </x-slot>

    <div class="p-4 sm:p-6 space-y-6">

        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center space-x-2">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

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

        <!-- Filter & Parameters Info Bar -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.schedules.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="grade_level" value="{{ $selectedGrade }}">
                @if($selectedClass)
                    <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                @endif

                <div class="flex items-center space-x-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Tahun Ajaran:</label>
                    <select name="academic_year_id" onchange="this.form.submit()" class="text-xs font-semibold rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                {{ $year->name }} ({{ $year->semester == 'odd' ? 'Ganjil' : 'Genap' }}) {{ $year->is_active ? '• Aktif' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <!-- Active Timing Badges -->
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-200 font-semibold">
                    ⏱️ 1 JP = <strong>{{ $scheduleConfig['lesson_duration'] ?? 40 }} Menit</strong>
                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 border border-slate-200 font-semibold">
                    🔔 Masuk: <strong>{{ $scheduleConfig['start_time'] ?? '07:00' }}</strong> &bull; Pulang: <strong>{{ $scheduleConfig['end_time'] ?? '14:00' }}</strong> (Jumat: {{ $scheduleConfig['friday_end_time'] ?? '11:15' }})
                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-amber-50 text-amber-800 border border-amber-200 font-semibold">
                    ☕ {{ count($scheduleConfig['recesses'] ?? []) }}x Istirahat
                </span>
            </div>
        </div>

        <!-- LEVEL 1 NAVIGATION: TINGKATAN TABS (7, 8, 9) -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50/50 px-4 pt-3 flex flex-wrap gap-2">
                @foreach($gradeLevels as $gKey => $gLabel)
                    @php
                        $isActiveGrade = ($selectedGrade == $gKey);
                    @endphp
                    <a href="{{ route('admin.schedules.index', ['grade_level' => $gKey, 'academic_year_id' => $selectedYearId]) }}"
                       class="px-5 py-3 rounded-t-xl text-sm font-extrabold flex items-center transition relative border-t border-x {{ $isActiveGrade ? 'bg-white text-blue-700 border-slate-200 -mb-px shadow-xs' : 'text-slate-600 hover:text-slate-900 border-transparent hover:bg-slate-100/70' }}">
                        <span class="w-2.5 h-2.5 rounded-full me-2 {{ $isActiveGrade ? 'bg-blue-600 ring-4 ring-blue-100' : 'bg-slate-300' }}"></span>
                        {{ $gLabel }}
                    </a>
                @endforeach
            </div>

            <!-- LEVEL 2 NAVIGATION: KELAS SUB-TABS (7-A, 7-B, etc.) -->
            <div class="p-4 bg-slate-50/30 border-b border-slate-200">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center space-x-2 overflow-x-auto pb-1 max-w-full">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap me-1">Pilih Kelas:</span>
                        @forelse($classesInGrade as $c)
                            @php
                                $isActiveClass = ($selectedClass && $selectedClass->id === $c->id);
                            @endphp
                            <a href="{{ route('admin.schedules.index', ['grade_level' => $selectedGrade, 'class_id' => $c->id, 'academic_year_id' => $selectedYearId]) }}"
                               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition whitespace-nowrap flex items-center {{ $isActiveClass ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-400' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300' }}">
                                <span>Kelas {{ $c->name }}</span>
                                @if($c->enrollments_count > 0)
                                    <span class="ms-1.5 px-1.5 py-0.2 rounded text-[10px] {{ $isActiveClass ? 'bg-blue-700 text-blue-100' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $c->enrollments_count }} siswa
                                    </span>
                                @endif
                            </a>
                        @empty
                            <span class="text-xs text-slate-400 italic">Belum ada data rombel kelas untuk tingkat ini.</span>
                        @endforelse
                    </div>

                    @if($selectedClass)
                        <div class="text-xs text-slate-600 flex items-center bg-white px-3 py-1.5 rounded-lg border border-slate-200">
                            <span class="font-bold text-slate-800 me-1">Wali Kelas:</span>
                            {{ $selectedClass->homeroomTeacher?->user->name ?? 'Belum ditentukan' }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- LEVEL 3: TIMETABLE MATRIX FOR SELECTED CLASS -->
            @if(! $selectedClass)
                <div class="p-12 text-center text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <p class="text-sm font-bold text-slate-700">Tidak ada rombel kelas yang ditemukan di tingkat ini.</p>
                    <p class="text-xs text-slate-400 mt-1">Silakan tambahkan data kelas terlebih dahulu di menu Rombel Kelas.</p>
                </div>
            @else
                <div class="p-4 sm:p-5">
                    <div class="flex items-center justify-between mb-3 no-print">
                        <h3 class="text-sm font-extrabold text-slate-900 flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 me-2"></span>
                            Matriks Jadwal Mingguan: Kelas {{ $selectedClass->name }} (Tingkat {{ $selectedGrade }})
                        </h3>
                        <div class="flex items-center space-x-2">
                            <button onclick="window.print()" type="button" class="px-3 py-1.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-lg shadow-2xs flex items-center transition cursor-pointer">
                                <svg class="w-4 h-4 me-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Cetak Jadwal Kelas
                            </button>
                            <span class="text-xs text-slate-500 font-semibold">
                                Total: <strong>{{ $matrixSchedules->count() }}</strong> Jam Pertemuan
                            </span>
                        </div>
                    </div>

                    <!-- Print Only Header -->
                    <div class="hidden print-only mb-4 text-center border-b pb-3">
                        <h2 class="text-lg font-black uppercase text-slate-900">SMP NEGERI 2 SUBANG</h2>
                        <h3 class="text-sm font-bold text-slate-700">JADWAL PELAJARAN KELAS {{ $selectedClass->name }} — TAHUN AJARAN {{ $academicYears->firstWhere('id', $selectedYearId)?->name ?? '' }}</h3>
                        <p class="text-xs text-slate-500">Wali Kelas: {{ $selectedClass->homeroomTeacher?->user->name ?? 'Belum ditentukan' }} &bull; KBM: 07:00 – 14:00 WIB</p>
                    </div>

                    <!-- Weekly Timetable Matrix Grid -->
                    <div class="overflow-x-auto border border-slate-200 rounded-xl shadow-xs">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-100 text-slate-700 uppercase font-black tracking-wider text-[11px] border-b border-slate-200 divide-x divide-slate-200">
                                    <th class="p-3 w-32 text-center bg-slate-200/70">Waktu / Sesi</th>
                                    <th class="p-3 text-center bg-blue-50/60 text-blue-900">Senin</th>
                                    <th class="p-3 text-center bg-emerald-50/60 text-emerald-900">Selasa</th>
                                    <th class="p-3 text-center bg-amber-50/60 text-amber-900">Rabu</th>
                                    <th class="p-3 text-center bg-purple-50/60 text-purple-900">Kamis</th>
                                    <th class="p-3 text-center bg-teal-50/60 text-teal-900">Jumat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @php
                                    $daysList = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                                    $fridayEndTime = $scheduleConfig['friday_end_time'] ?? '11:15';
                                    $renderedScheduleIds = [];
                                @endphp

                                @forelse($timelineMonday as $slot)
                                    @if($slot['type'] === 'recess')
                                        <!-- Recess Row (Full Width spanning all day columns) -->
                                        <tr class="bg-amber-50/80 border-y border-amber-200">
                                            <td class="p-2.5 text-center font-mono font-bold text-amber-900 border-r border-amber-200 whitespace-nowrap bg-amber-100/60">
                                                ☕ {{ $slot['start_time'] }} - {{ $slot['end_time'] }}
                                            </td>
                                            <td colspan="5" class="p-2.5 text-center text-amber-900 font-extrabold tracking-wide uppercase">
                                                ☕ {{ $slot['name'] }} ({{ $slot['duration'] }} Menit) — Istirahat & Relaksasi
                                            </td>
                                        </tr>
                                    @else
                                        <!-- Regular Lesson Period Row -->
                                        <tr class="hover:bg-slate-50/60 transition divide-x divide-slate-200">
                                            <!-- Period & Time Column -->
                                            <td class="p-2.5 text-center bg-slate-50/90 whitespace-nowrap">
                                                <div class="font-extrabold text-slate-800">{{ $slot['name'] }}</div>
                                                <div class="text-[10px] font-mono text-slate-500 font-bold">{{ $slot['start_time'] }} - {{ $slot['end_time'] }}</div>
                                                <div class="text-[9px] text-slate-400">({{ $slot['duration'] }} mnt)</div>
                                            </td>

                                            <!-- Day Columns: Senin, Selasa, Rabu, Kamis, Jumat -->
                                            @foreach($daysList as $dayKey)
                                                @php
                                                    $isFriday = ($dayKey === 'friday');
                                                    $isAfterFridayEnd = $isFriday && strcmp($slot['start_time'], $fridayEndTime) >= 0;
                                                    $sched = $scheduleMatrix[$dayKey][$slot['start_time']] ?? null;
                                                    if ($sched) {
                                                        $renderedScheduleIds[] = $sched->id;
                                                    }
                                                @endphp

                                                @if($isAfterFridayEnd)
                                                    <!-- Friday after-school hours -->
                                                    <td class="p-2 text-center bg-slate-100/70 text-slate-400 italic">
                                                        <div class="text-[10px] font-semibold">🕌 Shalat Jumat & Selesai KBM</div>
                                                        <div class="text-[9px] text-slate-400">Pulang pukul {{ $fridayEndTime }} WIB</div>
                                                    </td>
                                                @elseif($sched)
                                                    <!-- Filled Period -->
                                                    <td class="p-2.5 bg-blue-50/30 hover:bg-blue-100/40 transition">
                                                        <div class="space-y-1">
                                                            <div class="flex items-center justify-between">
                                                                <span class="font-bold text-slate-900 text-xs">
                                                                    {{ $sched->classSubject->subject->name }}
                                                                </span>
                                                                <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-blue-100 text-blue-800 font-bold">
                                                                    {{ $sched->classSubject->subject->code }}
                                                                </span>
                                                            </div>
                                                            <div class="text-[11px] text-slate-700 font-medium flex items-center">
                                                                <span class="me-1">👨‍🏫</span> {{ $sched->classSubject->teacher->user->name ?? '-' }}
                                                            </div>
                                                            <div class="flex items-center justify-between pt-1 border-t border-blue-200/60">
                                                                <span class="text-[10px] text-slate-500">
                                                                    🏛️ {{ $sched->room ?? 'Kelas ' . $selectedClass->name }}
                                                                </span>
                                                                <div class="inline-flex items-center space-x-1">
                                                                    <a href="{{ route('admin.schedules.edit', $sched) }}" class="text-[10px] font-bold text-amber-700 hover:underline">
                                                                        Edit
                                                                    </a>
                                                                    <span class="text-slate-300">&bull;</span>
                                                                    <form action="{{ route('admin.schedules.destroy', $sched) }}" method="POST" class="inline" onsubmit="return confirm('Hapus jadwal ini?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="text-[10px] font-bold text-red-600 hover:underline">
                                                                            Hapus
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                @else
                                                    <!-- Empty Period Slot -->
                                                    <td class="p-2.5 text-center group hover:bg-slate-50">
                                                        <span class="text-slate-300 text-xs">&mdash; Kosong &mdash;</span>
                                                        <div class="mt-1 opacity-0 group-hover:opacity-100 transition">
                                                            <a href="{{ route('admin.schedules.create') }}?day_of_week={{ $dayKey }}&start_time={{ $slot['start_time'] }}"
                                                               class="inline-block text-[10px] font-bold text-blue-600 hover:text-blue-800 bg-white px-2 py-0.5 rounded border border-blue-200 shadow-2xs">
                                                                + Isi
                                                            </a>
                                                        </div>
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-400">
                                            Belum ada slot waktu KBM yang terkonfigurasi. Buka menu "Atur Jam & Istirahat".
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Off-grid / Unmatched Schedules for Selected Class (if any) -->
                    @php
                        $unmatchedSchedules = $matrixSchedules->filter(function ($s) use ($renderedScheduleIds) {
                            return ! in_array($s->id, $renderedScheduleIds);
                        });
                    @endphp

                    @if($unmatchedSchedules->isNotEmpty())
                        <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center">
                                <span class="w-2 h-2 rounded-full bg-amber-500 me-2"></span>
                                Jadwal Pelajaran Jam Khusus / Manual Lainnya ({{ $unmatchedSchedules->count() }})
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($unmatchedSchedules as $us)
                                    <div class="bg-white p-3 rounded-lg border border-slate-200 text-xs space-y-1">
                                        <div class="flex items-center justify-between font-bold text-slate-900">
                                            <span>{{ $us->classSubject->subject->name }}</span>
                                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-slate-100 uppercase">{{ $us->day_of_week }}</span>
                                        </div>
                                        <div class="text-slate-600 font-mono font-bold text-[11px]">
                                            ⏰ {{ substr($us->start_time, 0, 5) }} - {{ substr($us->end_time, 0, 5) }}
                                        </div>
                                        <div class="text-slate-500 text-[11px]">
                                            👨‍🏫 {{ $us->classSubject->teacher->user->name ?? '-' }}
                                        </div>
                                        <div class="flex items-center justify-end space-x-2 pt-1 border-t border-slate-100">
                                            <a href="{{ route('admin.schedules.edit', $us) }}" class="text-amber-700 font-bold hover:underline">Edit</a>
                                            <form action="{{ route('admin.schedules.destroy', $us) }}" method="POST" class="inline" onsubmit="return confirm('Hapus?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 font-bold hover:underline">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- ACCORDION / TOGGLE: REKAP SELURUH JADWAL PER HARI (SENIN S/D JUMAT) -->
        <div x-data="{ openDaySummary: false }" class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <button @click="openDaySummary = !openDaySummary" type="button" class="w-full px-5 py-4 bg-slate-50 hover:bg-slate-100 text-left flex items-center justify-between transition border-b border-slate-200">
                <div>
                    <h3 class="font-extrabold text-sm text-slate-900 flex items-center">
                        <span class="me-2 text-base">📑</span> Rekap Seluruh Jadwal Sekolah per Hari (Semua Kelas)
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Lihat daftar jadwal gabungan seluruh rombel yang tersusun rapi dengan pembatas visual per hari.</p>
                </div>
                <div class="flex items-center text-xs font-bold text-blue-600">
                    <span x-text="openDaySummary ? 'Sembunyikan' : 'Buka Rekap'"></span>
                    <svg class="w-4 h-4 ms-1 transition-transform" :class="{ 'rotate-180': openDaySummary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>

            <div x-show="openDaySummary" x-cloak class="p-5 space-y-6">
                @foreach($daysMap as $dayKey => $dayMeta)
                    @php
                        $daySchedules = $schedulesByDay->get($dayKey, collect());
                    @endphp

                    <div class="rounded-xl border border-slate-200 overflow-hidden shadow-2xs">
                        <!-- Day Header Divider Banner -->
                        <div class="px-4 py-3 bg-slate-50 flex items-center justify-between border-l-4 {{ $dayMeta['border'] }}">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-black text-slate-900">
                                    📅 HARI {{ strtoupper($dayMeta['label']) }}
                                </span>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $dayMeta['badge'] }}">
                                    {{ $daySchedules->count() }} Pertemuan
                                </span>
                            </div>
                            <span class="text-xs text-slate-400 font-medium">Jam KBM 07:00 s/d 14:00 WIB</span>
                        </div>

                        @if($daySchedules->isEmpty())
                            <div class="p-4 text-center text-xs text-slate-400">Tidak ada jadwal KBM pada hari {{ $dayMeta['label'] }}.</div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead class="bg-slate-50/70 text-[10px] uppercase font-bold text-slate-500 border-b border-slate-200">
                                        <tr>
                                            <th class="px-3 py-2">Waktu KBM</th>
                                            <th class="px-3 py-2">Kelas</th>
                                            <th class="px-3 py-2">Mata Pelajaran</th>
                                            <th class="px-3 py-2">Guru Pengampu</th>
                                            <th class="px-3 py-2">Ruang</th>
                                            <th class="px-3 py-2 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($daySchedules as $sched)
                                            <tr class="hover:bg-slate-50 transition">
                                                <td class="px-3 py-2 font-mono font-bold text-blue-800 whitespace-nowrap">
                                                    {{ substr($sched->start_time, 0, 5) }} - {{ substr($sched->end_time, 0, 5) }}
                                                </td>
                                                <td class="px-3 py-2 font-bold text-slate-800">
                                                    Kelas {{ $sched->classSubject->schoolClass->name }}
                                                </td>
                                                <td class="px-3 py-2">
                                                    <span class="font-bold text-slate-900">{{ $sched->classSubject->subject->name }}</span>
                                                    <span class="text-[10px] text-slate-400 font-mono">({{ $sched->classSubject->subject->code }})</span>
                                                </td>
                                                <td class="px-3 py-2 text-slate-700">
                                                    {{ $sched->classSubject->teacher->user->name ?? '-' }}
                                                </td>
                                                <td class="px-3 py-2 text-slate-500">
                                                    {{ $sched->room ?? 'Kelas ' . $sched->classSubject->schoolClass->name }}
                                                </td>
                                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                                    <a href="{{ route('admin.schedules.edit', $sched) }}" class="text-amber-700 font-bold hover:underline me-2">Edit</a>
                                                    <form action="{{ route('admin.schedules.destroy', $sched) }}" method="POST" class="inline" onsubmit="return confirm('Hapus?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 font-bold hover:underline">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <style>
        @media print {
            nav, header, .no-print, [x-data] button, form, .border-b.bg-slate-50\/50, .border-b.border-slate-200 {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            th, td {
                border: 1px solid #cbd5e1 !important;
                padding: 6px !important;
            }
            .bg-blue-50\/30, .bg-slate-50, .bg-amber-50\/80 {
                background: transparent !important;
            }
        }
    </style>
</x-app-layout>
