<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Rekap Kehadiran Siswa') }}
        </h2>
    </x-slot>

    <div class="flex-1 flex flex-col min-h-0 p-4 sm:p-6 space-y-4">
        <!-- Student Header Banner -->
        <div class="bg-blue-600 text-white rounded-xl p-4 shadow-xs flex-shrink-0">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold">{{ $student->user->name }}</h3>
                    <p class="text-blue-100 text-xs mt-0.5">
                        NIS: {{ $student->nis }} | Kelas: {{ $student->currentEnrollment?->schoolClass?->name ?? 'Belum Ditentukan' }}
                    </p>
                </div>
                <div class="bg-white/10 px-3.5 py-1.5 rounded-lg text-right">
                    <span class="block text-[10px] uppercase text-blue-100 font-semibold">Persentase Kehadiran</span>
                    <span class="text-2xl font-extrabold">{{ $stats['percentage'] }}%</span>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 flex-shrink-0">
            <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-xs border-l-4 border-l-slate-400">
                <span class="text-[10px] text-slate-400 font-semibold uppercase">Total Pertemuan</span>
                <p class="text-xl font-bold text-slate-800 mt-0.5">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-xs border-l-4 border-l-green-500">
                <span class="text-[10px] text-slate-400 font-semibold uppercase">Hadir</span>
                <p class="text-xl font-bold text-green-600 mt-0.5">{{ $stats['present'] }}</p>
            </div>
            <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-xs border-l-4 border-l-blue-500">
                <span class="text-[10px] text-slate-400 font-semibold uppercase">Izin</span>
                <p class="text-xl font-bold text-blue-600 mt-0.5">{{ $stats['excused'] }}</p>
            </div>
            <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-xs border-l-4 border-l-amber-500">
                <span class="text-[10px] text-slate-400 font-semibold uppercase">Sakit</span>
                <p class="text-xl font-bold text-amber-600 mt-0.5">{{ $stats['sick'] }}</p>
            </div>
            <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-xs border-l-4 border-l-red-500">
                <span class="text-[10px] text-slate-400 font-semibold uppercase">Alpa</span>
                <p class="text-xl font-bold text-red-600 mt-0.5">{{ $stats['absent'] }}</p>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs flex-shrink-0">
            <form method="GET" action="{{ route('student.attendances.index') }}" class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Filter Mata Pelajaran</label>
                    <select name="class_subject_id" onchange="this.form.submit()" class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <option value="">Semua Mata Pelajaran</option>
                        @foreach($classSubjects as $cs)
                            <option value="{{ $cs->id }}" {{ request('class_subject_id') == $cs->id ? 'selected' : '' }}>
                                {{ $cs->subject->name }} (Guru: {{ $cs->teacher->user->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-blue-700 transition">
                        Filter
                    </button>
                    <a href="{{ route('student.attendances.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold uppercase hover:bg-slate-200 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Attendance History Table Card: Fills empty space & scrolls internally -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs flex-1 flex flex-col min-h-0 overflow-hidden">
            <div class="overflow-auto flex-1 min-h-0">
                <table class="w-full text-left text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500 sticky top-0 z-10 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 bg-slate-50">Tanggal</th>
                            <th class="px-4 py-3 bg-slate-50">Mata Pelajaran</th>
                            <th class="px-4 py-3 bg-slate-50">Guru</th>
                            <th class="px-4 py-3 bg-slate-50">Topik / Materi</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Status</th>
                            <th class="px-4 py-3 bg-slate-50">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($attendances as $att)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 font-semibold text-slate-800">
                                    {{ $att->attendanceSession->session_date->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    {{ $att->attendanceSession->classSubject->subject->name }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $att->attendanceSession->classSubject->teacher->user->name }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $att->attendanceSession->topic ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $badgeColor = match($att->status) {
                                            'present' => 'bg-green-100 text-green-800',
                                            'excused' => 'bg-blue-100 text-blue-800',
                                            'sick' => 'bg-amber-100 text-amber-800',
                                            'absent' => 'bg-red-100 text-red-800',
                                        };
                                        $labelIndo = match($att->status) {
                                            'present' => 'Hadir',
                                            'excused' => 'Izin',
                                            'sick' => 'Sakit',
                                            'absent' => 'Alpa',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $badgeColor }}">
                                        {{ $labelIndo }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500 text-xs">
                                    {{ $att->note ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                    Belum ada rekaman kehadiran untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($attendances->total() > 0)
                <div class="px-4 py-3 border-t border-slate-200 bg-white flex-shrink-0">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
