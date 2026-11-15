<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Kelola Mata Pelajaran') }}
            </h2>
            <a href="{{ route('admin.subjects.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                + Tambah Mata Pelajaran
            </a>
        </div>
    </x-slot>

    <div class="flex-1 flex flex-col min-h-0 p-4 sm:p-6 space-y-4">
        <!-- Search & Filter Card -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs flex flex-col md:flex-row justify-between items-center gap-3 flex-shrink-0">
            <form method="GET" action="{{ route('admin.subjects.index') }}" class="flex w-full md:w-80 gap-2 items-center">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode atau nama mapel..." class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500" />
                <button type="submit" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.subjects.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm transition">
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
                            <th class="px-4 py-3 bg-slate-50">Kode</th>
                            <th class="px-4 py-3 bg-slate-50">Nama Mata Pelajaran</th>
                            <th class="px-4 py-3 bg-slate-50">Deskripsi</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($subjects as $subject)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 font-mono font-bold text-blue-700">{{ $subject->code }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $subject->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $subject->description ?? '-' }}</td>
                                <td class="px-4 py-3 text-center space-x-2 whitespace-nowrap">
                                    <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="text-xs bg-amber-50 text-amber-700 hover:bg-amber-100 px-2.5 py-1.5 rounded-md font-semibold transition">
                                        Ubah
                                    </a>
                                    <form method="POST" action="{{ route('admin.subjects.destroy', $subject->id) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?');">
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
                                <td colspan="4" class="px-4 py-12 text-center text-slate-400">
                                    Belum ada data mata pelajaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($subjects->total() > 0)
                <div class="px-4 py-3 border-t border-slate-200 bg-white flex-shrink-0">
                    {{ $subjects->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
