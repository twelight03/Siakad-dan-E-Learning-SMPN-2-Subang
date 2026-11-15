<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Detail Sesi Absensi') }}
            </h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('teacher.attendances.edit', $session->id) }}" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 transition shadow-xs">
                    Edit Sesi
                </a>
                <a href="{{ route('teacher.attendances.index', ['class_subject_id' => $session->class_subject_id]) }}" class="inline-flex items-center px-4 py-2 bg-slate-100 border border-slate-200 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-200 transition">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="flex-1 flex flex-col min-h-0 p-4 sm:p-6 space-y-4">
        <!-- Info Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 flex-shrink-0">
            <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs border-l-4 border-l-blue-600">
                <span class="text-xs text-slate-400 font-semibold uppercase">Kelas & Mata Pelajaran</span>
                <p class="text-sm font-bold text-slate-900 mt-0.5">
                    {{ $session->classSubject->schoolClass->name }} - {{ $session->classSubject->subject->name }}
                </p>
                <span class="text-xs text-slate-400">Guru: {{ $session->classSubject->teacher->user->name }}</span>
            </div>
            <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs border-l-4 border-l-indigo-600">
                <span class="text-xs text-slate-400 font-semibold uppercase">Tanggal & Topik</span>
                <p class="text-sm font-bold text-slate-900 mt-0.5">
                    {{ $session->session_date->format('d F Y') }}
                </p>
                <span class="text-xs text-slate-400 truncate block">{{ $session->topic ?? 'Tidak ada catatan materi' }}</span>
            </div>
            <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs border-l-4 border-l-green-600">
                <span class="text-xs text-slate-400 font-semibold uppercase">Tingkat Kehadiran</span>
                @php
                    $total = $session->attendances->count();
                    $present = $session->attendances->where('status', 'present')->count();
                    $pct = $total > 0 ? round(($present / $total) * 100, 1) : 0;
                @endphp
                <p class="text-xl font-black text-green-600 mt-0.5">
                    {{ $pct }}%
                </p>
                <span class="text-xs text-slate-400">{{ $present }} dari {{ $total }} siswa hadir</span>
            </div>
            <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs flex items-center justify-around text-center">
                <div>
                    <span class="block text-lg font-bold text-green-600">{{ $present }}</span>
                    <span class="text-xs text-slate-400">Hadir</span>
                </div>
                <div>
                    <span class="block text-lg font-bold text-blue-600">{{ $session->attendances->where('status', 'excused')->count() }}</span>
                    <span class="text-xs text-slate-400">Izin</span>
                </div>
                <div>
                    <span class="block text-lg font-bold text-amber-600">{{ $session->attendances->where('status', 'sick')->count() }}</span>
                    <span class="text-xs text-slate-400">Sakit</span>
                </div>
                <div>
                    <span class="block text-lg font-bold text-red-600">{{ $session->attendances->where('status', 'absent')->count() }}</span>
                    <span class="text-xs text-slate-400">Alpa</span>
                </div>
            </div>
        </div>

        <!-- Attendance Detail Table Card: Fills empty space & scrolls internally -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs flex-1 flex flex-col min-h-0 overflow-hidden">
            <div class="px-6 py-3 bg-slate-50 border-b border-slate-200 flex-shrink-0">
                <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">
                    Rincian Kehadiran Siswa
                </h3>
            </div>
            <div class="overflow-auto flex-1 min-h-0">
                <table class="w-full text-left text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase sticky top-0 z-10 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 bg-slate-50 text-center w-12">No</th>
                            <th class="px-4 py-3 bg-slate-50">NIS</th>
                            <th class="px-4 py-3 bg-slate-50">Nama Siswa</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Status</th>
                            <th class="px-4 py-3 bg-slate-50">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($session->attendances as $index => $att)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-center text-slate-400 font-mono text-xs">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-mono text-slate-700">{{ $att->student->nis }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $att->student->user->name }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($att->status === 'present')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Hadir</span>
                                    @elseif($att->status === 'excused')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Izin</span>
                                    @elseif($att->status === 'sick')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">Sakit</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Alpa</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-500 text-xs">
                                    {{ $att->note ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
