<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ubah Penugasan Guru ke Kelas') }}
            </h2>
            <a href="{{ route('admin.class-subjects.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.class-subjects.update', $classSubject->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="academic_year_id" value="Tahun Ajaran" />
                        <select id="academic_year_id" name="academic_year_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id', $classSubject->academic_year_id) == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }} ({{ $year->semester == 'odd' ? 'Ganjil' : 'Genap' }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('academic_year_id')" />
                    </div>

                    <div>
                        <x-input-label for="class_id" value="Rombel / Kelas" />
                        <select id="class_id" name="class_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ old('class_id', $classSubject->class_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} (Tingkat {{ $c->grade_level }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('class_id')" />
                    </div>

                    <div>
                        <x-input-label for="subject_id" value="Mata Pelajaran" />
                        <select id="subject_id" name="subject_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}" {{ old('subject_id', $classSubject->subject_id) == $sub->id ? 'selected' : '' }}>
                                    [{{ $sub->code }}] {{ $sub->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('subject_id')" />
                    </div>

                    <div>
                        <x-input-label for="teacher_id" value="Guru Pengampu" />
                        <select id="teacher_id" name="teacher_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ old('teacher_id', $classSubject->teacher_id) == $t->id ? 'selected' : '' }}>
                                    {{ $t->user->name }} (NIP: {{ $t->nip ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('teacher_id')" />
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('admin.class-subjects.index') }}" class="px-4 py-2 border rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                            Perbarui Penugasan
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
