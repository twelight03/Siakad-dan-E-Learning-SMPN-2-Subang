<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Tugas Pembelajaran E-Learning') }}
        </h2>
    </x-slot>

    <div class="flex-1 flex flex-col min-h-0 p-4 sm:p-6 space-y-4">
        <!-- Student Header Banner -->
        <div class="bg-blue-600 text-white rounded-xl p-4 shadow-xs flex-shrink-0">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold">{{ $student->user->name }}</h3>
                    <p class="text-blue-100 text-xs mt-0.5">
                        Kelas: {{ $currentEnrollment?->schoolClass?->name ?? 'Belum Ditentukan' }} | NIS: {{ $student->nis }}
                    </p>
                </div>
                <div class="bg-white/10 px-3.5 py-1.5 rounded-lg text-right">
                    <span class="block text-[10px] uppercase text-blue-100 font-semibold">Tugas Terdaftar</span>
                    <span class="text-lg font-black">{{ $assignments->total() ?? 0 }} Tugas</span>
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
                                <th class="px-4 py-3 bg-slate-50">Judul Tugas</th>
                                <th class="px-4 py-3 bg-slate-50">Batas Waktu (Deadline)</th>
                                <th class="px-4 py-3 bg-slate-50 text-center">Status Anda</th>
                                <th class="px-4 py-3 bg-slate-50 text-center">Nilai</th>
                                <th class="px-4 py-3 bg-slate-50 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($assignments as $assignment)
                                @php
                                    $submission = $assignment->submissions->first();
                                    $isPassedDeadline = now()->gt($assignment->deadline);
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 font-semibold text-slate-900">
                                        {{ $assignment->classSubject->subject->name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-800">{{ $assignment->title }}</div>
                                        @if($assignment->attachment_path)
                                            <span class="text-xs text-blue-600">📎 Ada Lampiran Soal</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs {{ $isPassedDeadline && ! $submission ? 'text-red-600 font-semibold' : 'text-slate-700' }}">
                                        {{ $assignment->deadline->format('d/m/Y H:i') }} WIB
                                        @if($isPassedDeadline)
                                            <span class="block text-slate-400 font-normal">Sudah Berakhir</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($submission)
                                            @if($submission->status === 'late')
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                                                    Dikumpulkan (Terlambat)
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Dikumpulkan (Tepat Waktu)
                                                </span>
                                            @endif
                                        @else
                                            @if($isPassedDeadline)
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    Tidak Mengumpulkan
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-700">
                                                    Belum Mengumpulkan
                                                </span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($submission?->grade)
                                            <span class="text-base font-extrabold text-green-600">{{ (float)$submission->grade->score }}</span>
                                        @else
                                            <span class="text-xs text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('student.assignments.show', $assignment->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md text-xs font-semibold hover:bg-blue-700 shadow-xs transition">
                                            Buka Tugas &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                        Belum ada tugas e-learning untuk kelas Anda.
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
        @endif
    </div>
</x-app-layout>
