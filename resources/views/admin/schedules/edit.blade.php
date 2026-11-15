<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ubah Jadwal Pelajaran') }}
            </h2>
            <a href="{{ route('admin.schedules.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.schedules.update', $schedule->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="class_subject_id" value="Penugasan Kelas & Mapel" />
                        <select id="class_subject_id" name="class_subject_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required autofocus>
                            @foreach($classSubjects as $cs)
                                <option value="{{ $cs->id }}" {{ old('class_subject_id', $schedule->class_subject_id) == $cs->id ? 'selected' : '' }}>
                                    Kelas {{ $cs->schoolClass->name }} — {{ $cs->subject->name }} (Guru: {{ $cs->teacher->user->name }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('class_subject_id')" />
                    </div>

                    <div>
                        <x-input-label for="day_of_week" value="Hari Belajar" />
                        <select id="day_of_week" name="day_of_week" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            <option value="monday" {{ old('day_of_week', $schedule->day_of_week) == 'monday' ? 'selected' : '' }}>Senin</option>
                            <option value="tuesday" {{ old('day_of_week', $schedule->day_of_week) == 'tuesday' ? 'selected' : '' }}>Selasa</option>
                            <option value="wednesday" {{ old('day_of_week', $schedule->day_of_week) == 'wednesday' ? 'selected' : '' }}>Rabu</option>
                            <option value="thursday" {{ old('day_of_week', $schedule->day_of_week) == 'thursday' ? 'selected' : '' }}>Kamis</option>
                            <option value="friday" {{ old('day_of_week', $schedule->day_of_week) == 'friday' ? 'selected' : '' }}>Jumat</option>
                            <option value="saturday" {{ old('day_of_week', $schedule->day_of_week) == 'saturday' ? 'selected' : '' }}>Sabtu</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('day_of_week')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="start_time" value="Jam Mulai" />
                            <x-text-input id="start_time" name="start_time" type="time" class="mt-1 block w-full" :value="old('start_time', substr($schedule->start_time, 0, 5))" required />
                            <x-input-error class="mt-2" :messages="$errors->get('start_time')" />
                        </div>

                        <div>
                            <x-input-label for="end_time" value="Jam Selesai" />
                            <x-text-input id="end_time" name="end_time" type="time" class="mt-1 block w-full" :value="old('end_time', substr($schedule->end_time, 0, 5))" required />
                            <x-input-error class="mt-2" :messages="$errors->get('end_time')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="room" value="Ruangan / Tempat Belajar (Opsional)" />
                        <x-text-input id="room" name="room" type="text" class="mt-1 block w-full" :value="old('room', $schedule->room)" />
                        <x-input-error class="mt-2" :messages="$errors->get('room')" />
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('admin.schedules.index') }}" class="px-4 py-2 border rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                            Perbarui Jadwal
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
