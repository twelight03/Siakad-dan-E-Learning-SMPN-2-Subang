<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Tugas & Pengumpulan') }}
            </h2>
            <a href="{{ route('student.assignments.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Kembali ke Daftar Tugas
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded text-red-700 text-sm">
                    <p class="font-bold">Gagal mengumpulkan tugas:</p>
                    <ul class="list-disc list-inside mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Assignment Info -->
            <div class="bg-white p-6 shadow rounded-lg">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b pb-4">
                    <div>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded bg-blue-100 text-blue-800">
                            {{ $assignment->classSubject->subject->name }} (Kelas {{ $assignment->classSubject->schoolClass->name }})
                        </span>
                        <h3 class="text-2xl font-black text-gray-900 mt-2">{{ $assignment->title }}</h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Guru: <strong>{{ $assignment->creator->name }}</strong> | Batas Waktu: <strong class="{{ now()->gt($assignment->deadline) ? 'text-red-600' : 'text-gray-800' }}">{{ $assignment->deadline->format('d F Y, H:i') }} WIB</strong>
                        </p>
                    </div>

                    @if($assignment->attachment_path)
                        <div>
                            <a href="{{ route('student.assignments.attachment.download', $assignment->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-md text-xs font-bold shadow-sm">
                                📎 Unduh Berkas Soal
                            </a>
                        </div>
                    @endif
                </div>

                <div class="mt-4">
                    <h4 class="text-xs font-semibold uppercase text-gray-500">Petunjuk Tugas:</h4>
                    <p class="text-sm text-gray-700 mt-1 whitespace-pre-line">{{ $assignment->description }}</p>
                </div>
            </div>

            <!-- Grade Card (if graded) -->
            @if($submission && $submission->grade)
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase text-green-700">Hasil Penilaian Guru</span>
                            <h4 class="text-3xl font-black text-green-800 mt-1">
                                {{ (float)$submission->grade->score }} <span class="text-base font-normal text-green-600">/ 100</span>
                            </h4>
                        </div>
                        <div class="text-right text-xs text-green-700">
                            Dinilai oleh: <strong>{{ $submission->grade->grader->name }}</strong><br>
                            Tanggal: {{ $submission->grade->graded_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    @if($submission->grade->feedback)
                        <div class="mt-3 pt-3 border-t border-green-200 text-sm text-green-900">
                            <strong>Catatan Guru:</strong> {{ $submission->grade->feedback }}
                        </div>
                    @endif
                </div>
            @endif

            <!-- Submission Status & Form -->
            <div class="bg-white p-6 shadow rounded-lg space-y-6">
                <h4 class="text-lg font-bold text-gray-800 border-b pb-2">
                    {{ $submission ? 'Pengumpulan Jawaban Anda' : 'Kirim Jawaban Tugas' }}
                </h4>

                @if($submission)
                    <div class="bg-gray-50 p-4 rounded-lg border space-y-3">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-500">Waktu Pengumpulan: <strong>{{ $submission->submitted_at->format('d/m/Y H:i') }} WIB</strong></span>
                            @if($submission->status === 'late')
                                <span class="px-2.5 py-0.5 rounded-full font-bold bg-red-100 text-red-800">Terlambat</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full font-bold bg-green-100 text-green-800">Tepat Waktu</span>
                            @endif
                        </div>

                        @if($submission->file_path)
                            <div class="text-sm">
                                <span class="text-gray-600 font-medium">Berkas yang Anda Unggah:</span>
                                <a href="{{ route('student.assignments.submissions.download', $submission->id) }}" class="ms-2 inline-flex items-center text-blue-600 hover:text-blue-800 font-bold">
                                    📥 Unduh Berkas Jawaban Saya
                                </a>
                            </div>
                        @endif

                        @if($submission->text_answer)
                            <div>
                                <span class="text-xs font-semibold text-gray-600 block mb-1">Jawaban Teks:</span>
                                <div class="p-3 bg-white rounded border text-sm text-gray-800 whitespace-pre-line">
                                    {{ $submission->text_answer }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Upload / Revise Form -->
                <div>
                    <h5 class="text-sm font-bold text-gray-700 mb-3">
                        {{ $submission ? 'Perbarui / Unggah Ulang Jawaban' : 'Formulir Pengumpulan Jawaban' }}
                    </h5>
                    <form method="POST" action="{{ route('student.assignments.submit', $assignment->id) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Jawaban Tertulis (Opsional jika mengunggah file)</label>
                            <textarea name="text_answer" rows="4" placeholder="Tuliskan jawaban Anda langsung di sini..." class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('text_answer') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Unggah Berkas Jawaban (PDF, DOC, DOCX, ZIP, JPG, PNG - Maks 10 MB)</label>
                            <input type="file" name="submission_file" class="w-full text-sm border border-gray-300 rounded-md p-2 bg-gray-50 focus:outline-none focus:border-blue-500">
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-md shadow text-sm hover:bg-blue-700">
                                {{ $submission ? 'Kirim Pembaruan Jawaban' : 'Kumpulkan Tugas' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
