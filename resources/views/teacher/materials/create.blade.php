<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Unggah Materi Pembelajaran') }}
            </h2>
            <a href="{{ route('teacher.materials.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Kembali ke Daftar Materi
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6 space-y-6">
                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded text-red-700 text-sm">
                        <p class="font-bold">Gagal mengunggah materi:</p>
                        <ul class="list-disc list-inside mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('teacher.materials.store') }}" enctype="multipart/form-data" class="space-y-4">
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
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Materi <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Modul 1 - Bilangan Bulat dan Pecahan" required class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi / Petunjuk Baca</label>
                        <textarea name="description" rows="3" placeholder="Tuliskan petunjuk atau rangkuman singkat untuk siswa..." class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Berkas Materi <span class="text-red-500">*</span></label>
                        <input type="file" name="material_file" required class="w-full text-sm border border-gray-300 rounded-md p-2 bg-gray-50 focus:outline-none focus:border-blue-500">
                        <span class="text-xs text-gray-500 mt-1 block">Format diperbolehkan: PDF, DOC, DOCX, PPT, PPTX, ZIP, JPG, PNG. Ukuran maksimal: 10 MB.</span>
                    </div>

                    <div class="pt-4 border-t flex justify-end space-x-3">
                        <a href="{{ route('teacher.materials.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md shadow text-sm hover:bg-blue-700">
                            Unggah Materi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
