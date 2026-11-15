<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pindah Kelas Siswa') }}
            </h2>
            <a href="{{ route('admin.enrollments.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.enrollments.update', $enrollment->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="student_id" value="{{ $enrollment->student_id }}" />
                    <input type="hidden" name="academic_year_id" value="{{ $enrollment->academic_year_id }}" />

                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <div class="text-xs text-gray-500">Nama Siswa</div>
                        <div class="text-base font-bold text-gray-900 mt-1">{{ $enrollment->student->user->name }} (NIS: {{ $enrollment->student->nis }})</div>
                        <div class="text-xs text-gray-500 mt-2">Tahun Ajaran: {{ $enrollment->academicYear->name }} ({{ $enrollment->academicYear->semester == 'odd' ? 'Ganjil' : 'Genap' }})</div>
                    </div>

                    <div>
                        <x-input-label for="class_id" value="Pindah ke Rombel / Kelas Baru" />
                        <select id="class_id" name="class_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ old('class_id', $enrollment->class_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} (Tingkat {{ $c->grade_level }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('class_id')" />
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('admin.enrollments.index') }}" class="px-4 py-2 border rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                            Simpan Perubahan
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
