<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Kelola Rombel / Kelas') }}
            </h2>
            <a href="{{ route('admin.classes.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                + Tambah Rombel / Kelas
            </a>
        </div>
    </x-slot>

    <div class="flex-1 flex flex-col min-h-0 p-4 sm:p-6 space-y-4">
        <!-- Search & Filter Card -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs flex flex-col md:flex-row justify-between items-center gap-3 flex-shrink-0">
            <form method="GET" action="{{ route('admin.classes.index') }}" class="flex flex-wrap w-full md:w-auto items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama rombel..." class="w-full md:w-64 text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500" />
                <select name="grade_level" onchange="this.form.submit()" class="text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                    <option value="">Semua Tingkat</option>
                    <option value="7" {{ request('grade_level') == '7' ? 'selected' : '' }}>Tingkat 7</option>
                    <option value="8" {{ request('grade_level') == '8' ? 'selected' : '' }}>Tingkat 8</option>
                    <option value="9" {{ request('grade_level') == '9' ? 'selected' : '' }}>Tingkat 9</option>
                </select>
                <button type="submit" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                    Filter
                </button>
                @if(request('search') || request('grade_level'))
                    <a href="{{ route('admin.classes.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Table Card: Fills empty space & scrolls internally -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs flex-1 flex flex-col min-h-0 overflow-hidden">
            <div class="overflow-auto flex-1 min-h-0">
                <table class="w-full text-left text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-500 sticky top-0 z-10 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 bg-slate-50">Nama Kelas</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Tingkat</th>
                            <th class="px-4 py-3 bg-slate-50">Wali Kelas</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Jumlah Siswa</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($classes as $class)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $class->name }}</td>
                                <td class="px-4 py-3 text-center font-bold text-slate-600">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">Kelas {{ $class->grade_level }}</span>
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    {{ $class->homeroomTeacher?->user->name ?? 'Belum ditentukan' }}
                                </td>
                                <td class="px-4 py-3 text-center text-slate-800 font-semibold">
                                    {{ $class->enrollments_count }} Siswa
                                </td>
                                <td class="px-4 py-3 text-center space-x-2 whitespace-nowrap">
                                    <a href="{{ route('admin.classes.edit', $class->id) }}" class="text-xs bg-amber-50 text-amber-700 hover:bg-amber-100 px-2.5 py-1.5 rounded-md font-semibold transition">
                                        Ubah
                                    </a>
                                    <form method="POST" action="{{ route('admin.classes.destroy', $class->id) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rombel / kelas ini?');">
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
                                    Belum ada data rombel / kelas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($classes->total() > 0)
                <div class="px-4 py-3 border-t border-slate-200 bg-white flex-shrink-0">
                    {{ $classes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
