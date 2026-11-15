<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ubah Tahun Ajaran') }}
            </h2>
            <a href="{{ route('admin.academic-years.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.academic-years.update', $academicYear->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Nama Tahun Ajaran" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $academicYear->name)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="semester" value="Semester" />
                        <select id="semester" name="semester" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="odd" {{ old('semester', $academicYear->semester) == 'odd' ? 'selected' : '' }}>Ganjil (Odd)</option>
                            <option value="even" {{ old('semester', $academicYear->semester) == 'even' ? 'selected' : '' }}>Genap (Even)</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('semester')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="start_date" value="Tanggal Mulai" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', $academicYear->start_date?->format('Y-m-d'))" required />
                            <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                        </div>
                        <div>
                            <x-input-label for="end_date" value="Tanggal Selesai" />
                            <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="old('end_date', $academicYear->end_date?->format('Y-m-d'))" required />
                            <x-input-error class="mt-2" :messages="$errors->get('end_date')" />
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $academicYear->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" />
                        <label for="is_active" class="ms-2 text-sm text-gray-700 font-medium">Jadikan sebagai tahun ajaran aktif</label>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('admin.academic-years.index') }}" class="px-4 py-2 border rounded-lg text-sm text-gray-700 hover:bg-gray-50">
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
