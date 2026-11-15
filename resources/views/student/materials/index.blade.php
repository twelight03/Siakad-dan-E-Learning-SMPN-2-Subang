<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Materi Pembelajaran Saya') }}
        </h2>
    </x-slot>

    <div class="flex-1 flex flex-col min-h-0 p-4 sm:p-6 space-y-4">
        <!-- Student Header Banner -->
        <div class="bg-blue-600 text-white rounded-xl p-4 shadow-xs flex-shrink-0">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold">{{ $student->user->name }}</h3>
                    <p class="text-blue-100 text-xs mt-0.5">
                        Kelas: {{ $currentEnrollment?->schoolClass?->name ?? 'Belum Ditentukan' }} | Tahun Ajaran: {{ $currentEnrollment?->academicYear?->name ?? '-' }}
                    </p>
                </div>
                <div class="bg-white/10 px-3.5 py-1.5 rounded-lg text-right">
                    <span class="block text-[10px] uppercase text-blue-100 font-semibold">Total Materi Tersedia</span>
                    <span class="text-lg font-black">{{ $materials->total() ?? 0 }} Berkas</span>
                </div>
            </div>
        </div>

        @if(! $currentEnrollment)
            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-xl text-amber-800 text-sm">
                Anda belum terdaftar dalam kelas aktif untuk tahun ajaran ini.
            </div>
        @else
            <!-- Table Card: Fills empty space & scrolls internally -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs flex-1 flex flex-col min-h-0 overflow-hidden">
                <div class="overflow-auto flex-1 min-h-0">
                    <table class="w-full text-left text-sm text-slate-600 border-collapse">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500 sticky top-0 z-10 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 bg-slate-50">Mata Pelajaran</th>
                                <th class="px-4 py-3 bg-slate-50">Judul & Keterangan</th>
                                <th class="px-4 py-3 bg-slate-50">Guru Pengajar</th>
                                <th class="px-4 py-3 bg-slate-50 text-center">Ukuran</th>
                                <th class="px-4 py-3 bg-slate-50">Tanggal Unggah</th>
                                <th class="px-4 py-3 bg-slate-50 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($materials as $material)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 font-semibold text-slate-900">
                                        {{ $material->classSubject->subject->name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-800">{{ $material->title }}</div>
                                        @if($material->description)
                                            <div class="text-xs text-slate-400 mt-0.5">{{ $material->description }}</div>
                                        @endif
                                        <div class="text-xs text-slate-400 font-mono mt-1">{{ $material->original_filename }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 text-xs">
                                        {{ $material->uploader->name }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs text-slate-500">
                                        {{ round($material->file_size / 1024, 1) }} KB
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 text-xs">
                                        {{ $material->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('student.materials.download', $material->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md text-xs font-semibold hover:bg-blue-700 shadow-xs transition">
                                            Unduh Materi
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                        Belum ada materi pembelajaran yang dibagikan untuk kelas Anda saat ini.
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
        @endif
    </div>
</x-app-layout>
