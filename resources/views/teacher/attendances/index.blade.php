<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Absensi & Kehadiran Siswa') }}
            </h2>
            <a href="{{ route('teacher.attendances.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 shadow-xs transition">
                + Catat Absensi Baru
            </a>
        </div>
    </x-slot>

    <div class="flex-1 flex flex-col min-h-0 p-4 sm:p-6 space-y-4">
        <!-- Filter Card -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs flex-shrink-0">
            <form method="GET" action="{{ route('teacher.attendances.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Kelas & Mata Pelajaran</label>
                    <select name="class_subject_id" onchange="this.form.submit()" class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <option value="">Semua Kelas & Mata Pelajaran</option>
                        @foreach($myClassSubjects as $cs)
                            <option value="{{ $cs->id }}" {{ request('class_subject_id') == $cs->id ? 'selected' : '' }}>
                                Kelas {{ $cs->schoolClass->name }} - {{ $cs->subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Sesi</label>
                    <input type="date" name="session_date" value="{{ request('session_date') }}" onchange="this.form.submit()" class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                </div>
                <div class="flex items-center space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold uppercase transition flex-1">
                        Filter
                    </button>
                    <a href="{{ route('teacher.attendances.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold uppercase transition text-center flex-1">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Card: Fills empty space & scrolls internally -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs flex-1 flex flex-col min-h-0 overflow-hidden">
            <div class="overflow-auto flex-1 min-h-0">
                <table class="w-full text-left text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-semibold sticky top-0 z-10 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 bg-slate-50">Tanggal</th>
                            <th class="px-4 py-3 bg-slate-50">Kelas</th>
                            <th class="px-4 py-3 bg-slate-50">Mata Pelajaran</th>
                            <th class="px-4 py-3 bg-slate-50">Topik / Materi</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Rekap Kehadiran</th>
                            <th class="px-4 py-3 bg-slate-50 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($sessions as $session)
                            @php
                                $total = $session->attendances->count();
                                $present = $session->attendances->where('status', 'present')->count();
                                $excused = $session->attendances->where('status', 'excused')->count();
                                $sick = $session->attendances->where('status', 'sick')->count();
                                $absent = $session->attendances->where('status', 'absent')->count();
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 font-semibold text-slate-800">
                                    {{ $session->session_date->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    {{ $session->classSubject->schoolClass->name }}
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    {{ $session->classSubject->subject->name }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $session->topic ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="inline-flex space-x-1 text-xs">
                                        <span class="px-2 py-0.5 rounded bg-green-100 text-green-800" title="Hadir">H: {{ $present }}</span>
                                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800" title="Izin">I: {{ $excused }}</span>
                                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800" title="Sakit">S: {{ $sick }}</span>
                                        <span class="px-2 py-0.5 rounded bg-red-100 text-red-800" title="Alpa">A: {{ $absent }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('teacher.attendances.show', $session->id) }}" class="text-xs bg-blue-50 text-blue-700 hover:bg-blue-100 px-2.5 py-1.5 rounded-md font-semibold transition">
                                        Detail
                                    </a>
                                    <a href="{{ route('teacher.attendances.edit', $session->id) }}" class="text-xs bg-amber-50 text-amber-700 hover:bg-amber-100 px-2.5 py-1.5 rounded-md font-semibold transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('teacher.attendances.destroy', $session->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sesi absensi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs bg-red-50 text-red-700 hover:bg-red-100 px-2.5 py-1.5 rounded-md font-semibold transition">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                    Belum ada sesi absensi yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($sessions->total() > 0)
                <div class="px-4 py-3 border-t border-slate-200 bg-white flex-shrink-0">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
