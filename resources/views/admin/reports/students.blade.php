<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Laporan Daftar Siswa per Kelas') }}
            </h2>
            <div class="space-x-2">
                @if($selectedClass && $selectedYear)
                    <a href="{{ route('admin.reports.students', ['class_id' => $selectedClass->id, 'academic_year_id' => $selectedYear->id, 'print' => 1]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition shadow-xs">
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
            <form method="GET" action="{{ route('admin.reports.students') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tahun Ajaran</label>
                    <select name="academic_year_id" onchange="this.form.submit()" class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $selectedYear && $selectedYear->id == $year->id ? 'selected' : '' }}>
                                {{ $year->name }} ({{ ucfirst($year->semester == 'odd' ? 'Ganjil' : 'Genap') }}) {{ $year->is_active ? '★ Aktif' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Kelas</label>
                    <select name="class_id" onchange="this.form.submit()" class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClass && $selectedClass->id == $c->id ? 'selected' : '' }}>
                                Kelas {{ $c->name }} (Tingkat {{ $c->grade_level }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 shadow-xs transition">
                        Tampilkan Laporan
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Card: Fills empty space & scrolls internally -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs flex-1 flex flex-col min-h-0 overflow-hidden">
            <div class="px-6 py-3 bg-slate-50 border-b border-slate-200 flex justify-between items-center flex-shrink-0">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">
                        Daftar Siswa Kelas {{ $selectedClass?->name ?? '-' }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Wali Kelas: {{ $selectedClass?->homeroomTeacher?->user->name ?? 'Belum Ditentukan' }} | Total: {{ $enrollments->count() }} Siswa
                    </p>
                </div>
            </div>

            <div class="overflow-auto flex-1 min-h-0">
                <table class="w-full text-left text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500 sticky top-0 z-10 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 bg-slate-50 text-center w-12">No</th>
                            <th class="px-4 py-3 bg-slate-50">NIS</th>
                            <th class="px-4 py-3 bg-slate-50">Nama Lengkap Siswa</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">L/P</th>
                            <th class="px-4 py-3 bg-slate-50">Tanggal Lahir</th>
                            <th class="px-4 py-3 bg-slate-50">No. Telepon</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($enrollments as $index => $enr)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-center text-slate-400 font-mono text-xs">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-mono text-slate-800 font-semibold">{{ $enr->student->nis }}</td>
                                <td class="px-4 py-3 font-bold text-slate-900">{{ $enr->student->user->name }}</td>
                                <td class="px-4 py-3 text-center font-medium">{{ $enr->student->gender === 'male' ? 'L' : 'P' }}</td>
                                <td class="px-4 py-3 text-slate-600 text-xs">{{ $enr->student->birth_date?->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-600 text-xs">{{ $enr->student->phone ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                    Tidak ada siswa yang terdaftar pada kelas dan tahun ajaran yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
