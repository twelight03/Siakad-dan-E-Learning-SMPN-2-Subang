<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Transkrip Nilai Akademik Siswa') }}
        </h2>
    </x-slot>

    <div class="p-4 sm:p-6 w-full space-y-4">
        <!-- Student Header Banner -->
        <div class="bg-blue-600 text-white rounded-xl p-4 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold">{{ $student->user->name }}</h3>
                    <p class="text-blue-100 text-xs mt-0.5">
                        NIS: {{ $student->nis }} | Kelas: {{ $currentEnrollment?->schoolClass?->name ?? 'Belum Ditentukan' }}
                    </p>
                </div>
                <div class="bg-white/10 px-3.5 py-1.5 rounded-lg text-right">
                    <span class="block text-[10px] uppercase text-blue-100 font-semibold">Tahun Ajaran</span>
                    <span class="text-sm font-bold">{{ $currentEnrollment?->academicYear?->name ?? '-' }} ({{ ucfirst($currentEnrollment?->academicYear?->semester ?? '-') }})</span>
                </div>
            </div>
        </div>

        @if($subjectReports->isEmpty())
            <div class="bg-white p-8 rounded-xl border border-slate-200 shadow-xs text-center text-slate-400">
                Belum ada data mata pelajaran atau nilai yang tercatat untuk Anda pada periode ini.
            </div>
        @else
            <div class="space-y-4">
                @foreach($subjectReports as $report)
                    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden border-t-4 {{ $report['isPassed'] ? 'border-t-green-500' : 'border-t-amber-500' }}">
                        <!-- Header of Subject Card -->
                        <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
                            <div>
                                <h4 class="text-base font-bold text-slate-900">{{ $report['subject']->name }}</h4>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Guru: <strong>{{ $report['teacher'] }}</strong> | KKM: <strong>{{ $report['kkm'] }}</strong>
                                </p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 uppercase font-semibold block">Nilai Akhir</span>
                                    <span class="text-xl font-black {{ $report['isPassed'] ? 'text-green-600' : 'text-amber-600' }}">
                                        {{ $report['finalScore'] }}
                                    </span>
                                </div>
                                <div class="text-center">
                                    <span class="text-[10px] text-slate-400 uppercase font-semibold block">Predikat</span>
                                    <span class="px-2 py-0.5 text-xs font-black rounded {{ $report['predicate'] === 'A' ? 'bg-green-100 text-green-800' : ($report['predicate'] === 'B' ? 'bg-blue-100 text-blue-800' : ($report['predicate'] === 'C' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800')) }}">
                                        {{ $report['predicate'] }}
                                    </span>
                                </div>
                                <div>
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $report['isPassed'] ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $report['isPassed'] ? 'Tuntas' : 'Remedial' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Components Breakdown Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600 border-collapse">
                                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase border-b border-slate-200">
                                    <tr>
                                        <th class="px-4 py-2.5">Komponen Penilaian</th>
                                        <th class="px-4 py-2.5 text-center">Bobot</th>
                                        <th class="px-4 py-2.5 text-center">Nilai Saya</th>
                                        <th class="px-4 py-2.5">Catatan / Umpan Balik Guru</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($report['components'] as $comp)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-4 py-2.5 font-medium text-slate-800">
                                                {{ $comp['name'] }}
                                            </td>
                                            <td class="px-4 py-2.5 text-center text-slate-600 font-semibold">
                                                {{ $comp['weight'] }}%
                                            </td>
                                            <td class="px-4 py-2.5 text-center font-bold {{ $comp['score'] !== null ? ($comp['score'] >= $report['kkm'] ? 'text-green-600' : 'text-red-600') : 'text-slate-400' }}">
                                                {{ $comp['score'] !== null ? $comp['score'] : 'Belum dinilai' }}
                                            </td>
                                            <td class="px-4 py-2.5 text-xs text-slate-500">
                                                {{ $comp['feedback'] ?? '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-4 text-center text-xs text-slate-400 italic">
                                                Belum ada komponen penilaian yang ditentukan untuk mata pelajaran ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
