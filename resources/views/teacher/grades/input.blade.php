<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Input Nilai:') }} {{ $grade_component->name }}
            </h2>
            <a href="{{ route('teacher.grade-components.index', ['class_subject_id' => $grade_component->class_subject_id]) }}" class="text-sm text-slate-500 hover:text-blue-600 transition font-medium">
                &larr; Kembali ke Komponen
            </a>
        </div>
    </x-slot>

    <div class="flex-1 flex flex-col min-h-0 p-4 sm:p-6 space-y-4">
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl text-red-700 text-sm flex-shrink-0">
                <p class="font-bold">Terdapat kesalahan pengisian nilai:</p>
                <ul class="list-disc list-inside mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Meta Information -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs grid grid-cols-2 md:grid-cols-4 gap-4 flex-shrink-0">
            <div>
                <span class="text-xs font-semibold uppercase text-slate-400">Mata Pelajaran</span>
                <p class="text-sm font-bold text-slate-800 mt-0.5">{{ $grade_component->classSubject->subject->name }}</p>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase text-slate-400">Kelas</span>
                <p class="text-sm font-bold text-slate-800 mt-0.5">Kelas {{ $grade_component->classSubject->schoolClass->name }}</p>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase text-slate-400">Bobot Komponen</span>
                <p class="text-sm font-bold text-blue-600 mt-0.5">{{ $grade_component->weight }}%</p>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase text-slate-400">KKM Mapel</span>
                <p class="text-sm font-bold text-green-600 mt-0.5">{{ $grade_component->classSubject->subject->kkm }}</p>
            </div>
        </div>

        <!-- Grade Input Table Card -->
        <form method="POST" action="{{ route('teacher.grades.store', $grade_component->id) }}" class="flex-1 flex flex-col min-h-0">
            @csrf
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs flex-1 flex flex-col min-h-0 overflow-hidden">
                <div class="px-6 py-3 bg-slate-50 border-b border-slate-200 flex justify-between items-center flex-shrink-0">
                    <h3 class="font-bold text-slate-800 text-sm">
                        Daftar Siswa & Pengisian Nilai (Skala 0 - {{ (int)$grade_component->max_score }})
                    </h3>
                    <span class="text-xs text-slate-500">
                        Total: <strong>{{ $enrollments->count() }} Siswa</strong>
                    </span>
                </div>

                @if($enrollments->isEmpty())
                    <div class="p-12 text-center text-slate-400 text-sm flex-1">
                        Belum ada siswa yang terdaftar di kelas ini.
                    </div>
                @else
                    <div class="overflow-auto flex-1 min-h-0">
                        <table class="w-full text-left text-sm text-slate-600 border-collapse">
                            <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase sticky top-0 z-10 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3 bg-slate-50 text-center w-12">No</th>
                                    <th class="px-4 py-3 bg-slate-50">NIS</th>
                                    <th class="px-4 py-3 bg-slate-50">Nama Siswa</th>
                                    <th class="px-4 py-3 bg-slate-50 text-center w-36">Nilai (0 - 100)</th>
                                    <th class="px-4 py-3 bg-slate-50">Catatan / Umpan Balik Guru</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($enrollments as $index => $enrollment)
                                    @php
                                        $existing = $existingGrades->get($enrollment->student_id);
                                        $currentScore = old("grades.{$index}.score", $existing?->score);
                                        $currentFeedback = old("grades.{$index}.feedback", $existing?->feedback);
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-4 py-3 text-center text-slate-400 font-mono text-xs">
                                            {{ $index + 1 }}
                                            <input type="hidden" name="grades[{{ $index }}][student_id]" value="{{ $enrollment->student_id }}">
                                        </td>
                                        <td class="px-4 py-3 font-mono text-slate-700">
                                            {{ $enrollment->student->nis }}
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-slate-900">
                                            {{ $enrollment->student->user->name }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="number" name="grades[{{ $index }}][score]" value="{{ $currentScore !== null ? (float)$currentScore : '' }}" min="0" max="100" step="0.1" placeholder="0 - 100" class="w-28 text-center text-sm font-bold rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" name="grades[{{ $index }}][feedback]" value="{{ $currentFeedback }}" placeholder="Catatan untuk siswa (opsional)..." class="w-full text-xs rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex justify-between items-center flex-shrink-0">
                        <span class="text-xs text-slate-400">
                            Pastikan skor antara 0 - 100 sebelum menyimpan.
                        </span>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold shadow-xs transition">
                            Simpan Seluruh Nilai
                        </button>
                    </div>
                @endif
            </div>
        </form>
    </div>
</x-app-layout>
