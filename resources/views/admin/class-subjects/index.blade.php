<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Penugasan Guru ke Kelas & Mapel') }}
            </h2>
            <a href="{{ route('admin.class-subjects.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                + Tambah Penugasan
            </a>
        </div>
    </x-slot>

    <div class="flex-1 flex flex-col min-h-0 p-4 sm:p-6 space-y-4">
        <!-- Filter & Search Card -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs flex-shrink-0">
            <form method="GET" action="{{ route('admin.class-subjects.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="text-xs font-semibold text-slate-600 block mb-1">Tahun Ajaran</label>
                    <select name="academic_year_id" onchange="this.form.submit()" class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                {{ $year->name }} ({{ $year->semester == 'odd' ? 'Ganjil' : 'Genap' }}) {{ $year->is_active ? '★ Aktif' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 block mb-1">Pilih Kelas</label>
                    <select name="class_id" onchange="this.form.submit()" class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} (Tingkat {{ $c->grade_level }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 block mb-1">Pilih Guru</label>
                    <select name="teacher_id" onchange="this.form.submit()" class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <option value="">Semua Guru</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" {{ request('teacher_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition flex-1 sm:flex-none">
                        Terapkan
                    </button>
                    <a href="{{ route('admin.class-subjects.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm transition text-center flex-1 sm:flex-none">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Card: Fills empty space & scrolls internally -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs flex-1 flex flex-col min-h-0 overflow-hidden">
            <div class="overflow-auto flex-1 min-h-0">
                <table class="w-full text-left text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-500 sticky top-0 z-10 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 bg-slate-50">Kelas / Rombel</th>
                            <th class="px-4 py-3 bg-slate-50">Mata Pelajaran</th>
                            <th class="px-4 py-3 bg-slate-50">Guru Pengampu</th>
                            <th class="px-4 py-3 bg-slate-50">Tahun Ajaran</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($classSubjects as $cs)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 font-bold text-slate-800">{{ $cs->schoolClass->name }}</td>
                                <td class="px-4 py-3 font-semibold text-blue-700">
                                    [{{ $cs->subject->code }}] {{ $cs->subject->name }}
                                </td>
                                <td class="px-4 py-3 text-slate-800">
                                    {{ $cs->teacher->user->name }}
                                    <div class="text-xs text-slate-400 font-mono">NIP: {{ $cs->teacher->nip ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $cs->academicYear->name }} ({{ $cs->academicYear->semester == 'odd' ? 'Ganjil' : 'Genap' }})
                                </td>
                                <td class="px-4 py-3 text-center space-x-2 whitespace-nowrap">
                                    <a href="{{ route('admin.class-subjects.edit', $cs->id) }}" class="text-xs bg-amber-50 text-amber-700 hover:bg-amber-100 px-2.5 py-1.5 rounded-md font-semibold transition">
                                        Ubah
                                    </a>
                                    <form method="POST" action="{{ route('admin.class-subjects.destroy', $cs->id) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus penugasan guru ini?');">
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
                                    Belum ada data penugasan guru untuk filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($classSubjects->total() > 0)
                <div class="px-4 py-3 border-t border-slate-200 bg-white flex-shrink-0">
                    {{ $classSubjects->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
