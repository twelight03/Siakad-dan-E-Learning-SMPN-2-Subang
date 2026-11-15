<x-print-layout :title="'Rekap Absensi ' . ($selectedClassSubject?->schoolClass->name ?? '') . ' - ' . ($selectedClassSubject?->subject->name ?? '')">
    <div style="text-align: center; margin-bottom: 15px;">
        <h3 style="margin: 0; text-transform: uppercase;">REKAPITULASI KEHADIRAN / ABSENSI SISWA</h3>
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
            <td><strong>Total Pertemuan</strong></td>
            <td>: {{ $totalSessions }} Sesi</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">NIS</th>
                <th style="width: 35%;">Nama Lengkap Siswa</th>
                <th style="width: 10%;">Hadir (H)</th>
                <th style="width: 10%;">Izin (I)</th>
                <th style="width: 10%;">Sakit (S)</th>
                <th style="width: 10%;">Alpa (A)</th>
                <th style="width: 15%;">% Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($studentsRecap as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="text-align: center; font-family: monospace;">{{ $item['student']->nis }}</td>
                    <td><strong>{{ $item['student']->user->name }}</strong></td>
                    <td style="text-align: center;">{{ $item['present'] }}</td>
                    <td style="text-align: center;">{{ $item['excused'] }}</td>
                    <td style="text-align: center;">{{ $item['sick'] }}</td>
                    <td style="text-align: center;">{{ $item['absent'] }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $item['percentage'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">Belum ada catatan absensi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</x-print-layout>
