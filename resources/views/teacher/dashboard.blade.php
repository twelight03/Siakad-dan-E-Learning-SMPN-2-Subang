<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Guru') }}
            </h2>
            <span class="text-sm bg-green-100 text-green-800 font-medium px-3 py-1 rounded-full">
                {{ $teacher ? $teacher->user->name : 'Profil Guru' }} (NIP: {{ $teacher?->nip ?? '-' }})
            </span>
        </div>
    </x-slot>

    <div class="p-4 sm:p-6 w-full space-y-4">
            <!-- Summary row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500">Kelas & Mapel Diampu</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $classSubjects->count() }}</div>
                    <div class="mt-1 text-xs text-gray-400">Tahun Ajaran Aktif</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500">Jadwal Mengajar Hari Ini</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $todaySchedules->count() }}</div>
                    <div class="mt-1 text-xs text-gray-400">Sesi kelas</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-sm font-medium text-gray-500">Tugas Sedang Aktif</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $activeAssignments->count() }}</div>
                    <div class="mt-1 text-xs text-gray-400">Belum melewati batas waktu</div>
                </div>
            </div>

            <!-- Class subjects table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Penugasan Mengajar</h3>
                @if($classSubjects->isEmpty())
                    <p class="text-gray-500 text-sm">Belum ada kelas atau mata pelajaran yang ditugaskan kepada Anda pada tahun ajaran aktif.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500">Kelas</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500">Kode & Nama Mapel</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500">Tahun Ajaran</th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-500">Aksi Cepat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($classSubjects as $cs)
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $cs->schoolClass->name }}</td>
                                        <td class="px-4 py-3 text-gray-700">[{{ $cs->subject->code }}] {{ $cs->subject->name }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ $cs->academicYear->name }} ({{ $cs->academicYear->semester == 'odd' ? 'Ganjil' : 'Genap' }})</td>
                                        <td class="px-4 py-3 text-center space-x-2">
                                            <a href="{{ Route::has('teacher.attendances.index') ? route('teacher.attendances.index', ['class_subject_id' => $cs->id]) : '#' }}" class="text-xs bg-blue-50 text-blue-700 px-2.5 py-1 rounded hover:bg-blue-100 font-medium">Absensi</a>
                                            <a href="{{ Route::has('teacher.grade-components.index') ? route('teacher.grade-components.index', ['class_subject_id' => $cs->id]) : '#' }}" class="text-xs bg-green-50 text-green-700 px-2.5 py-1 rounded hover:bg-green-100 font-medium">Nilai</a>
                                            <a href="{{ Route::has('teacher.materials.index') ? route('teacher.materials.index', ['class_subject_id' => $cs->id]) : '#' }}" class="text-xs bg-purple-50 text-purple-700 px-2.5 py-1 rounded hover:bg-purple-100 font-medium">Materi</a>
                                            <a href="{{ Route::has('teacher.assignments.index') ? route('teacher.assignments.index', ['class_subject_id' => $cs->id]) : '#' }}" class="text-xs bg-yellow-50 text-yellow-700 px-2.5 py-1 rounded hover:bg-yellow-100 font-medium">Tugas</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Today schedule -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Jadwal Mengajar Hari Ini</h3>
                @if($todaySchedules->isEmpty())
                    <p class="text-gray-500 text-sm">Tidak ada jadwal mengajar untuk hari ini.</p>
                @else
                    <div class="space-y-3">
                        @foreach($todaySchedules as $sched)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <div>
                                    <span class="font-bold text-gray-800">{{ $sched->classSubject->schoolClass->name }}</span>
                                    <span class="mx-2 text-gray-400">•</span>
                                    <span class="text-gray-700 font-medium">{{ $sched->classSubject->subject->name }}</span>
                                    <span class="ms-3 text-xs text-gray-500">Ruangan: {{ $sched->room ?? 'Ruang Kelas' }}</span>
                                </div>
                                <div class="text-sm font-semibold text-blue-600">
                                    {{ substr($sched->start_time, 0, 5) }} - {{ substr($sched->end_time, 0, 5) }} WIB
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
</x-app-layout>
