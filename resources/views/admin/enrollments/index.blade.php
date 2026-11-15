<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Penempatan Siswa ke Rombel / Kelas') }}
            </h2>
            <a href="{{ route('admin.enrollments.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                + Daftarkan Siswa ke Kelas
            </a>
        </div>
    </x-slot>

    <div class="flex-1 flex flex-col min-h-0 p-4 sm:p-6 space-y-4">
        <!-- Filter & Search Card -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs flex-shrink-0">
            <form method="GET" action="{{ route('admin.enrollments.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
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
                    <label class="text-xs font-semibold text-slate-600 block mb-1">Cari Siswa</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="NIS atau nama siswa..." class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500" />
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition flex-1 sm:flex-none">
                        Terapkan
                    </button>
                    <a href="{{ route('admin.enrollments.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm transition text-center flex-1 sm:flex-none">
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
                            <th class="px-4 py-3 bg-slate-50">NIS</th>
                            <th class="px-4 py-3 bg-slate-50">Nama Siswa</th>
                            <th class="px-4 py-3 bg-slate-50">Kelas / Rombel</th>
                            <th class="px-4 py-3 bg-slate-50">Tahun Ajaran</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($enrollments as $enr)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 font-mono font-bold text-slate-700">{{ $enr->student->nis }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $enr->student->user->name }}</td>
                                <td class="px-4 py-3 font-bold text-blue-700">{{ $enr->schoolClass->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $enr->academicYear->name }} ({{ $enr->academicYear->semester == 'odd' ? 'Ganjil' : 'Genap' }})</td>
                                <td class="px-4 py-3 text-center space-x-2 whitespace-nowrap">
                                    <a href="{{ route('admin.enrollments.edit', $enr->id) }}" class="text-xs bg-amber-50 text-amber-700 hover:bg-amber-100 px-2.5 py-1.5 rounded-md font-semibold transition">
                                        Pindah Kelas
                                    </a>
                                    <form method="POST" action="{{ route('admin.enrollments.destroy', $enr->id) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pendaftaran siswa ini dari kelas?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs bg-red-50 text-red-700 hover:bg-red-100 px-2.5 py-1.5 rounded-md font-semibold transition">
                                            Batalkan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                                    Tidak ada siswa terdaftar pada kelas dan periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($enrollments->total() > 0)
                <div class="px-4 py-3 border-t border-slate-200 bg-white flex-shrink-0">
                    {{ $enrollments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
