<x-print-layout :title="'Rekap Tugas ' . ($selectedClassSubject?->schoolClass->name ?? '') . ' - ' . ($selectedClassSubject?->subject->name ?? '')">
    <div style="text-align: center; margin-bottom: 15px;">
        <h3 style="margin: 0; text-transform: uppercase;">REKAPITULASI PENUGASAN E-LEARNING</h3>
        <p style="margin: 5px 0; font-size: 11pt;">Semester {{ ucfirst($selectedClassSubject?->academicYear->semester ?? '-') }} Tahun Ajaran {{ $selectedClassSubject?->academicYear->name ?? '-' }}</p>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 18%;"><strong>Mata Pelajaran</strong></td>
            <td style="width: 32%;">: {{ $selectedClassSubject?->subject->name ?? '-' }}</td>
            <td style="width: 18%;"><strong>Guru Pengajar</strong></td>
            <td style="width: 32%;">: {{ $selectedClassSubject?->teacher->user->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Kelas</strong></td>
            <td>: {{ $selectedClassSubject?->schoolClass->name ?? '-' }}</td>
            <td><strong>Total Penugasan</strong></td>
            <td>: {{ $assignments->count() }} Tugas</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Judul Penugasan</th>
                <th style="width: 20%;">Batas Waktu</th>
                <th style="width: 10%;">Tepat Waktu</th>
                <th style="width: 10%;">Terlambat</th>
                <th style="width: 12%;">Telah Dinilai</th>
                <th style="width: 13%;">Rata-rata Skor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assignments as $index => $ass)
                @php
                    $totalSubs = $ass->submissions->count();
                    $onTime = $ass->submissions->where('status', 'submitted')->count();
                    $late = $ass->submissions->where('status', 'late')->count();
                    $graded = $ass->submissions->whereNotNull('grade')->count();
                    $avgScore = $graded > 0 ? round($ass->submissions->whereNotNull('grade')->avg('grade.score'), 1) : '-';
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td><strong>{{ $ass->title }}</strong></td>
                    <td style="text-align: center; font-size: 8.5pt;">{{ $ass->deadline->format('d/m/Y H:i') }} WIB</td>
                    <td style="text-align: center;">{{ $onTime }}</td>
                    <td style="text-align: center;">{{ $late }}</td>
                    <td style="text-align: center;">{{ $graded }} / {{ $totalSubs }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $avgScore }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">Belum ada penugasan yang tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</x-print-layout>
