<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Materi Pembelajaran E-Learning') }}
            </h2>
            <a href="{{ route('teacher.materials.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 shadow-xs transition">
                + Unggah Materi Baru
            </a>
        </div>
    </x-slot>

    <div class="flex-1 flex flex-col min-h-0 p-4 sm:p-6 space-y-4">
        <!-- Filter Card -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs flex-shrink-0">
            <form method="GET" action="{{ route('teacher.materials.index') }}" class="flex flex-col sm:flex-row gap-3 items-end">
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
                    <a href="{{ route('teacher.materials.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold uppercase transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Materials Table Card: Fills empty space & scrolls internally -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs flex-1 flex flex-col min-h-0 overflow-hidden">
            <div class="overflow-auto flex-1 min-h-0">
                <table class="w-full text-left text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase sticky top-0 z-10 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 bg-slate-50">Judul Materi</th>
                            <th class="px-4 py-3 bg-slate-50">Kelas & Mapel</th>
                            <th class="px-4 py-3 bg-slate-50">Nama Berkas</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Ukuran</th>
                            <th class="px-4 py-3 bg-slate-50">Tanggal Unggah</th>
                            <th class="px-4 py-3 bg-slate-50 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($materials as $material)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900">{{ $material->title }}</div>
                                    @if($material->description)
                                        <div class="text-xs text-slate-400 truncate max-w-xs">{{ $material->description }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    Kelas {{ $material->classSubject->schoolClass->name }} - {{ $material->classSubject->subject->name }}
                                </td>
                                <td class="px-4 py-3 text-slate-600 font-mono text-xs">
                                    {{ $material->original_filename }}
                                </td>
                                <td class="px-4 py-3 text-center text-xs text-slate-500">
                                    {{ round($material->file_size / 1024, 1) }} KB
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-xs">
                                    {{ $material->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('teacher.materials.download', $material->id) }}" class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-md text-xs font-semibold transition">
                                        Unduh
                                    </a>
                                    <form action="{{ route('teacher.materials.destroy', $material->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?');">
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
                                    Belum ada materi pembelajaran yang diunggah.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($materials->total() > 0)
                <div class="px-4 py-3 border-t border-slate-200 bg-white flex-shrink-0">
                    {{ $materials->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
