<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Kelola Tugas Siswa (E-Learning)') }}
            </h2>
            <a href="{{ route('teacher.assignments.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 shadow-xs transition">
                + Buat Tugas Baru
            </a>
        </div>
    </x-slot>

    <div class="flex-1 flex flex-col min-h-0 p-4 sm:p-6 space-y-4">
        <!-- Filter Card -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs flex-shrink-0">
            <form method="GET" action="{{ route('teacher.assignments.index') }}" class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Filter Kelas & Mata Pelajaran</label>
                    <select name="class_subject_id" onchange="this.form.submit()" class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <option value="">Semua Kelas & Mata Pelajaran</option>
                        @foreach($myClassSubjects as $cs)
                            <option value="{{ $cs->id }}" {{ request('class_subject_id') == $cs->id ? 'selected' : '' }}>
                                Kelas {{ $cs->schoolClass->name }} - {{ $cs->subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold uppercase transition">
                        Filter
                    </button>
                    <a href="{{ route('teacher.assignments.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold uppercase transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Card: Fills empty space & scrolls internally -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs flex-1 flex flex-col min-h-0 overflow-hidden">
            <div class="overflow-auto flex-1 min-h-0">
                <table class="w-full text-left text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase sticky top-0 z-10 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 bg-slate-50">Judul Tugas</th>
                            <th class="px-4 py-3 bg-slate-50">Kelas & Mapel</th>
                            <th class="px-4 py-3 bg-slate-50">Batas Waktu</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Status Pengumpulan</th>
                            <th class="px-4 py-3 bg-slate-50 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($assignments as $assignment)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900">{{ $assignment->title }}</div>
                                    @if($assignment->attachment_path)
                                        <span class="inline-flex items-center text-xs text-blue-600 mt-0.5">
                                            📎 Ada Lampiran
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    Kelas {{ $assignment->classSubject->schoolClass->name }} - {{ $assignment->classSubject->subject->name }}
                                </td>
                                <td class="px-4 py-3 text-xs {{ now()->gt($assignment->deadline) ? 'text-red-600 font-semibold' : 'text-slate-700' }}">
                                    {{ $assignment->deadline->format('d/m/Y H:i') }} WIB
                                    @if(now()->gt($assignment->deadline))
                                        <span class="block text-slate-400 font-normal">Telah berakhir</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="inline-flex space-x-1 text-xs">
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded font-semibold" title="Total Dikumpulkan">
                                            {{ $assignment->submissions_count }} Mengumpulkan
                                        </span>
                                        <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded font-semibold" title="Sudah Dinilai">
                                            {{ $assignment->graded_submissions_count }} Dinilai
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('teacher.assignments.show', $assignment->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md text-xs font-semibold hover:bg-blue-700 shadow-xs transition">
                                        Periksa & Nilai
                                    </a>
                                    <form action="{{ route('teacher.assignments.destroy', $assignment->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini beserta seluruh jawaban siswa?');">
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
                                <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                                    Belum ada tugas e-learning yang dibuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($assignments->total() > 0)
                <div class="px-4 py-3 border-t border-slate-200 bg-white flex-shrink-0">
                    {{ $assignments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
