<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ubah Rombel / Kelas') }}
            </h2>
            <a href="{{ route('admin.classes.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.classes.update', $class->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Nama Rombel / Kelas" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $class->name)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="grade_level" value="Tingkat / Jenjang Kelas" />
                        <select id="grade_level" name="grade_level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            <option value="7" {{ old('grade_level', $class->grade_level) == '7' ? 'selected' : '' }}>Kelas 7 (Tujuh)</option>
                            <option value="8" {{ old('grade_level', $class->grade_level) == '8' ? 'selected' : '' }}>Kelas 8 (Delapan)</option>
                            <option value="9" {{ old('grade_level', $class->grade_level) == '9' ? 'selected' : '' }}>Kelas 9 (Sembilan)</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('grade_level')" />
                    </div>

                    <div>
                        <x-input-label for="homeroom_teacher_id" value="Wali Kelas (Opsional)" />
                        <select id="homeroom_teacher_id" name="homeroom_teacher_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Belum Ditentukan / Tidak Ada</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('homeroom_teacher_id', $class->homeroom_teacher_id) == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->user->name }} (NIP: {{ $teacher->nip ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('homeroom_teacher_id')" />
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('admin.classes.index') }}" class="px-4 py-2 border rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                            Perbarui Data
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
