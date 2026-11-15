<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Buat Tugas E-Learning Baru') }}
            </h2>
            <a href="{{ route('teacher.assignments.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Kembali ke Daftar Tugas
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6 space-y-6">
                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded text-red-700 text-sm">
                        <p class="font-bold">Gagal membuat tugas:</p>
                        <ul class="list-disc list-inside mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('teacher.assignments.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kelas & Mata Pelajaran <span class="text-red-500">*</span></label>
                        <select name="class_subject_id" required class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Kelas & Mata Pelajaran</option>
                            @foreach($myClassSubjects as $cs)
                                <option value="{{ $cs->id }}" {{ old('class_subject_id') == $cs->id ? 'selected' : '' }}>
                                    Kelas {{ $cs->schoolClass->name }} - {{ $cs->subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Tugas <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Tugas 1 - Latihan Soal Aljabar" required class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Instruksi & Petunjuk Tugas <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="5" required placeholder="Tuliskan petunjuk pengerjaan tugas secara rinci untuk siswa..." class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Batas Waktu Pengumpulan (Deadline) <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="deadline" value="{{ old('deadline') }}" required class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Lampiran Berkas Soal / Lembar Kerja (Opsional)</label>
                        <input type="file" name="attachment" class="w-full text-sm border border-gray-300 rounded-md p-2 bg-gray-50 focus:outline-none focus:border-blue-500">
                        <span class="text-xs text-gray-500 mt-1 block">Format: PDF, DOC, DOCX, PPT, PPTX, ZIP, JPG, PNG (Maks 10 MB).</span>
                    </div>

                    <div class="pt-4 border-t flex justify-end space-x-3">
                        <a href="{{ route('teacher.assignments.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md shadow text-sm hover:bg-blue-700">
                            Terbitkan Tugas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
