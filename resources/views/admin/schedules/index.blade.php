<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Kelola Jadwal Pelajaran') }}
            </h2>
            <a href="{{ route('admin.schedules.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                + Tambah Jadwal
            </a>
        </div>
    </x-slot>

    <div class="flex-1 flex flex-col min-h-0 p-4 sm:p-6 space-y-4">
        <!-- Filter & Search Card -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs flex-shrink-0">
            <form method="GET" action="{{ route('admin.schedules.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="text-xs font-semibold text-slate-600 block mb-1">Tahun Ajaran</label>
                    <select name="academic_year_id" onchange="this.form.submit()" class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                {{ $year->name }} ({{ $year->semester == 'odd' ? 'Ganjil' : 'Genap' }})
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
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 block mb-1">Pilih Hari</label>
                    <select name="day_of_week" onchange="this.form.submit()" class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                        <option value="">Semua Hari</option>
                        <option value="monday" {{ request('day_of_week') == 'monday' ? 'selected' : '' }}>Senin</option>
                        <option value="tuesday" {{ request('day_of_week') == 'tuesday' ? 'selected' : '' }}>Selasa</option>
                        <option value="wednesday" {{ request('day_of_week') == 'wednesday' ? 'selected' : '' }}>Rabu</option>
                        <option value="thursday" {{ request('day_of_week') == 'thursday' ? 'selected' : '' }}>Kamis</option>
                        <option value="friday" {{ request('day_of_week') == 'friday' ? 'selected' : '' }}>Jumat</option>
                        <option value="saturday" {{ request('day_of_week') == 'saturday' ? 'selected' : '' }}>Sabtu</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition flex-1 sm:flex-none">
                        Filter
                    </button>
                    <a href="{{ route('admin.schedules.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm transition text-center flex-1 sm:flex-none">
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
                            <th class="px-4 py-3 bg-slate-50">Hari</th>
                            <th class="px-4 py-3 bg-slate-50">Waktu</th>
                            <th class="px-4 py-3 bg-slate-50">Kelas</th>
                            <th class="px-4 py-3 bg-slate-50">Mata Pelajaran</th>
                            <th class="px-4 py-3 bg-slate-50">Guru</th>
                            <th class="px-4 py-3 bg-slate-50">Ruangan</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $dayNames = [
                                'monday' => 'Senin',
                                'tuesday' => 'Selasa',
                                'wednesday' => 'Rabu',
                                'thursday' => 'Kamis',
                                'friday' => 'Jumat',
                                'saturday' => 'Sabtu',
                            ];
                        @endphp
                        @forelse($schedules as $sched)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 font-semibold text-slate-800">
                                    {{ $dayNames[$sched->day_of_week] ?? $sched->day_of_week }}
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-blue-600">
                                    {{ substr($sched->start_time, 0, 5) }} - {{ substr($sched->end_time, 0, 5) }} WIB
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-800">{{ $sched->classSubject->schoolClass->name }}</td>
                                <td class="px-4 py-3 text-slate-700">
                                    {{ $sched->classSubject->subject->name }}
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    {{ $sched->classSubject->teacher->user->name }}
                                </td>
                                <td class="px-4 py-3 text-slate-600 font-mono text-xs">
                                    {{ $sched->room ?? 'Ruang Kelas' }}
                                </td>
                                <td class="px-4 py-3 text-center space-x-2 whitespace-nowrap">
                                    <a href="{{ route('admin.schedules.edit', $sched->id) }}" class="text-xs bg-amber-50 text-amber-700 hover:bg-amber-100 px-2.5 py-1.5 rounded-md font-semibold transition">
                                        Ubah
                                    </a>
                                    <form method="POST" action="{{ route('admin.schedules.destroy', $sched->id) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
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
                                <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                                    Belum ada jadwal pelajaran untuk kriteria yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($schedules->total() > 0)
                <div class="px-4 py-3 border-t border-slate-200 bg-white flex-shrink-0">
                    {{ $schedules->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
