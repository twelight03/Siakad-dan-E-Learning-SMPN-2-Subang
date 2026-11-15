<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Laporan Rekapitulasi Absensi Siswa') }}
            </h2>
            <div class="space-x-2">
                @if($selectedClassSubject)
                    <a href="{{ route('admin.reports.attendances', ['class_subject_id' => $selectedClassSubject->id, 'print' => 1]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition shadow-xs">
                        🖨️ Cetak / Ekspor PDF
                    </a>
                @endif
                <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-200 transition">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="flex-1 flex flex-col min-h-0 p-4 sm:p-6 space-y-4">
        <!-- Filter Card -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs flex-shrink-0">
            <form method="GET" action="{{ route('admin.reports.attendances') }}" class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Kelas & Mata Pelajaran</label>
                    <select name="class_subject_id" onchange="this.form.submit()" class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        @foreach($classSubjects as $cs)
                            <option value="{{ $cs->id }}" {{ $selectedClassSubject && $selectedClassSubject->id == $cs->id ? 'selected' : '' }}>
                                Kelas {{ $cs->schoolClass->name }} - {{ $cs->subject->name }} (Guru: {{ $cs->teacher->user->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 shadow-xs transition">
                        Tampilkan Rekap
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Card: Fills empty space & scrolls internally -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs flex-1 flex flex-col min-h-0 overflow-hidden">
            <div class="px-6 py-3 bg-slate-50 border-b border-slate-200 flex justify-between items-center flex-shrink-0">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">
                        Rekap Absensi: Kelas {{ $selectedClassSubject?->schoolClass->name ?? '-' }} - {{ $selectedClassSubject?->subject->name ?? '-' }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Guru Pengajar: {{ $selectedClassSubject?->teacher->user->name ?? '-' }} | Total Pertemuan: {{ $totalSessions }} Sesi
                    </p>
                </div>
            </div>

            <div class="overflow-auto flex-1 min-h-0">
                <table class="w-full text-left text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500 sticky top-0 z-10 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 bg-slate-50 text-center w-12">No</th>
                            <th class="px-4 py-3 bg-slate-50">NIS</th>
                            <th class="px-4 py-3 bg-slate-50">Nama Siswa</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Hadir</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Izin</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Sakit</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Alpa</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">% Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($studentsRecap as $index => $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-center text-slate-400 font-mono text-xs">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-mono text-slate-800">{{ $item['student']->nis }}</td>
                                <td class="px-4 py-3 font-bold text-slate-900">{{ $item['student']->user->name }}</td>
                                <td class="px-4 py-3 text-center text-green-700 font-bold">{{ $item['present'] }}</td>
                                <td class="px-4 py-3 text-center text-blue-700 font-bold">{{ $item['excused'] }}</td>
                                <td class="px-4 py-3 text-center text-amber-700 font-bold">{{ $item['sick'] }}</td>
                                <td class="px-4 py-3 text-center text-red-700 font-bold">{{ $item['absent'] }}</td>
                                <td class="px-4 py-3 text-center font-black {{ $item['percentage'] >= 75 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $item['percentage'] }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                                    Tidak ada data kehadiran yang tercatat untuk kelas dan mata pelajaran ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
