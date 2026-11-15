<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Siswa') }}
            </h2>
            <span class="text-sm bg-blue-100 text-blue-800 font-medium px-3 py-1 rounded-full">
                {{ $student ? $student->user->name : 'Profil Siswa' }} (NIS: {{ $student?->nis ?? '-' }}) | Kelas: {{ $currentEnrollment?->schoolClass->name ?? 'Belum ada kelas' }}
            </span>
        </div>
    </x-slot>

    <div class="p-4 sm:p-6 w-full space-y-4">
            <!-- Attendance Summary -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-green-500">
                    <div class="text-xs text-gray-500">Hadir</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ $attendanceSummary['present'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
                    <div class="text-xs text-gray-500">Izin</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ $attendanceSummary['excused'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-yellow-500">
                    <div class="text-xs text-gray-500">Sakit</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ $attendanceSummary['sick'] }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-red-500">
                    <div class="text-xs text-gray-500">Alpa</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ $attendanceSummary['absent'] }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Today Schedules -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Jadwal Pelajaran Hari Ini</h3>
                    @if($todaySchedules->isEmpty())
                        <p class="text-sm text-gray-500">Tidak ada jadwal pelajaran untuk hari ini.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($todaySchedules as $sched)
                                <div class="p-3 bg-gray-50 rounded-lg flex justify-between items-center border border-gray-100">
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $sched->classSubject->subject->name }}</div>
                                        <div class="text-xs text-gray-500">Guru: {{ $sched->classSubject->teacher?->user->name ?? '-' }} | Ruang: {{ $sched->room ?? 'Kelas' }}</div>
                                    </div>
                                    <div class="text-sm font-semibold text-blue-600">
                                        {{ substr($sched->start_time, 0, 5) }} - {{ substr($sched->end_time, 0, 5) }} WIB
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Pending Assignments -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Tugas Perlu Dikerjakan</h3>
                    @if($pendingAssignments->isEmpty())
                        <p class="text-sm text-gray-500">Tidak ada tugas aktif yang belum dikumpulkan.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($pendingAssignments as $assignment)
                                <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-100 flex justify-between items-center">
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $assignment->title }}</div>
                                        <div class="text-xs text-gray-600">{{ $assignment->classSubject->subject->name }}</div>
                                        <div class="text-xs text-red-600 mt-1">Batas Waktu: {{ $assignment->deadline->format('d/m/Y H:i') }} WIB</div>
                                    </div>
                                    <a href="{{ Route::has('student.assignments.show') ? route('student.assignments.show', $assignment->id) : '#' }}" class="text-xs bg-yellow-500 text-white px-3 py-1.5 rounded hover:bg-yellow-600 font-medium">
                                        Kumpulkan
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recent Materials -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Materi Pembelajaran Terbaru</h3>
                    @if($recentMaterials->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada materi pembelajaran yang diunggah.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($recentMaterials as $mat)
                                <div class="p-3 bg-gray-50 rounded-lg flex justify-between items-center border border-gray-100">
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $mat->title }}</div>
                                        <div class="text-xs text-gray-500">{{ $mat->classSubject->subject->name }} • Oleh: {{ $mat->uploader?->name }}</div>
                                    </div>
                                    <a href="{{ Route::has('student.materials.download') ? route('student.materials.download', $mat->id) : '#' }}" class="text-xs bg-blue-50 text-blue-700 px-3 py-1.5 rounded hover:bg-blue-100 font-medium">
                                        Unduh
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Recent Grades -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Nilai Akademik Terbaru</h3>
                    @if($recentGrades->isEmpty())
                        <p class="text-sm text-gray-500">Belum ada catatan nilai akademik terbaru.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($recentGrades as $grade)
                                <div class="p-3 bg-gray-50 rounded-lg flex justify-between items-center border border-gray-100">
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $grade->gradeComponent->classSubject->subject->name }}</div>
                                        <div class="text-xs text-gray-500">Komponen: {{ $grade->gradeComponent->name }} (Bobot: {{ $grade->gradeComponent->weight }}%)</div>
                                        @if($grade->feedback)
                                            <div class="text-xs text-gray-600 italic mt-0.5">"{{ $grade->feedback }}"</div>
                                        @endif
                                    </div>
                                    <div class="text-lg font-bold text-green-600">
                                        {{ number_format($grade->score, 1) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
</x-app-layout>
