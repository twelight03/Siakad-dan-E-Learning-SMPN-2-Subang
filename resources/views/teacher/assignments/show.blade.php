<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Periksa Tugas & Jawaban Siswa') }}
            </h2>
            <a href="{{ route('teacher.assignments.index', ['class_subject_id' => $assignment->class_subject_id]) }}" class="text-sm text-slate-500 hover:text-blue-600 transition font-medium">
                &larr; Kembali ke Daftar Tugas
            </a>
        </div>
    </x-slot>

    <div class="p-4 sm:p-6 w-full space-y-4">
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl text-red-700 text-sm">
                <p class="font-bold">Gagal menyimpan penilaian:</p>
                <ul class="list-disc list-inside mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Assignment Info Card -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                        Kelas {{ $assignment->classSubject->schoolClass->name }} - {{ $assignment->classSubject->subject->name }}
                    </span>
                    <h3 class="text-xl font-black text-slate-900 mt-2">{{ $assignment->title }}</h3>
                    <p class="text-xs text-slate-400 mt-1">
                        Batas Waktu: <strong class="{{ now()->gt($assignment->deadline) ? 'text-red-600' : 'text-slate-700' }}">{{ $assignment->deadline->format('d F Y, H:i') }} WIB</strong>
                    </p>
                </div>

                @if($assignment->attachment_path)
                    <div>
                        <a href="{{ route('teacher.assignments.attachment.download', $assignment->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-bold transition shadow-xs">
                            📎 Unduh Lampiran Soal
                        </a>
                    </div>
                @endif
            </div>

            <div class="mt-4">
                <h4 class="text-xs font-semibold uppercase text-slate-400">Instruksi Tugas:</h4>
                <p class="text-sm text-slate-700 mt-1 whitespace-pre-line">{{ $assignment->description }}</p>
            </div>
        </div>

        <!-- Submissions Table Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-3 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-sm">
                    Daftar Pengumpulan Jawaban Siswa ({{ $assignment->submissions->count() }} Pengumpulan)
                </h3>
            </div>

            <div class="overflow-auto max-h-[calc(100vh-400px)]">
                <table class="w-full text-left text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase sticky top-0 z-10 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 bg-slate-50">Nama Siswa</th>
                            <th class="px-4 py-3 bg-slate-50">Waktu Pengumpulan</th>
                            <th class="px-4 py-3 bg-slate-50">Jawaban / Berkas</th>
                            <th class="px-4 py-3 bg-slate-50 text-center">Nilai</th>
                            <th class="px-4 py-3 bg-slate-50">Penilaian & Umpan Balik Guru</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($assignment->submissions as $sub)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900">{{ $sub->student->user->name }}</div>
                                    <div class="font-mono text-xs text-slate-400">NIS: {{ $sub->student->nis }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <div class="font-medium text-slate-700">{{ $sub->submitted_at->format('d/m/Y H:i') }} WIB</div>
                                    @if($sub->status === 'late')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-800">Terlambat</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-800">Tepat Waktu</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if($sub->file_path)
                                        <div class="mb-1">
                                            <a href="{{ route('teacher.assignments.submissions.download', $sub->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold">
                                                📄 Unduh Berkas Jawaban
                                            </a>
                                        </div>
                                    @endif
                                    @if($sub->text_answer)
                                        <div class="p-2 bg-slate-50 rounded border border-slate-200 text-slate-700 text-xs max-h-24 overflow-y-auto whitespace-pre-line">
                                            {{ $sub->text_answer }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($sub->grade)
                                        <span class="text-xl font-black text-green-600">{{ (float)$sub->grade->score }}</span>
                                        <span class="text-xs text-slate-400 block">/ 100</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-amber-100 text-amber-800 text-xs font-semibold">Belum Dinilai</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('teacher.assignments.submissions.grade', $sub->id) }}" class="flex flex-col sm:flex-row gap-2 items-center">
                                        @csrf
                                        <div class="w-24">
                                            <input type="number" name="score" value="{{ old('score', $sub->grade?->score ? (float)$sub->grade->score : '') }}" min="0" max="100" step="0.1" required placeholder="0-100" class="w-full text-center text-xs font-bold rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                        <div class="flex-1 w-full">
                                            <input type="text" name="feedback" value="{{ old('feedback', $sub->grade?->feedback) }}" placeholder="Catatan/feedback untuk siswa..." class="w-full text-xs rounded-lg border-slate-200 shadow-xs focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white font-semibold rounded-lg text-xs hover:bg-blue-700 shadow-xs whitespace-nowrap transition">
                                                Simpan
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                    Belum ada siswa yang mengumpulkan tugas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
