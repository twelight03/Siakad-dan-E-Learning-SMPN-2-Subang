<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Jadwal Pelajaran Saya') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6 w-full space-y-4">
        @if(! $currentEnrollment)
            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-xl text-amber-800 text-sm">
                Anda belum terdaftar dalam kelas aktif untuk tahun ajaran ini. Silakan hubungi admin sekolah.
            </div>
        @else
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-400 uppercase font-bold">Kelas Anda</span>
                    <h3 class="text-lg font-bold text-slate-900">{{ $currentEnrollment->schoolClass->name }}</h3>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-400 uppercase font-bold">Tahun Ajaran</span>
                    <p class="text-sm font-semibold text-blue-600">{{ $currentEnrollment->academicYear->name }} - {{ ucfirst($currentEnrollment->academicYear->semester == 'odd' ? 'Ganjil' : 'Genap') }}</p>
                </div>
            </div>

            @php
                $days = [
                    'monday' => 'Senin',
                    'tuesday' => 'Selasa',
                    'wednesday' => 'Rabu',
                    'thursday' => 'Kamis',
                    'friday' => 'Jumat',
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($days as $dayKey => $dayLabel)
                    @php
                        $daySchedules = $schedules->get($dayKey, collect());
                    @endphp
                    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden border-t-4 border-t-blue-600 flex flex-col">
                        <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex justify-between items-center">
                            <h4 class="font-bold text-slate-800">{{ $dayLabel }}</h4>
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-100 font-semibold">{{ $daySchedules->count() }} Mapel</span>
                        </div>
                        <div class="p-4 space-y-3 flex-1 overflow-y-auto max-h-96">
                            @forelse($daySchedules as $sched)
                                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 hover:border-blue-300 transition">
                                    <div class="flex justify-between items-start">
                                        <span class="text-xs font-mono font-bold text-blue-700">
                                            {{ substr($sched->start_time, 0, 5) }} - {{ substr($sched->end_time, 0, 5) }}
                                        </span>
                                        @if($sched->room)
                                            <span class="text-[10px] bg-slate-200 px-1.5 py-0.5 rounded text-slate-700 font-medium">Ruang: {{ $sched->room }}</span>
                                        @endif
                                    </div>
                                    <h5 class="font-semibold text-slate-900 text-sm mt-1">{{ $sched->classSubject->subject->name }}</h5>
                                    <p class="text-xs text-slate-400 mt-0.5">Guru: {{ $sched->classSubject->teacher->user->name }}</p>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic py-6 text-center">Tidak ada jadwal pelajaran.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
