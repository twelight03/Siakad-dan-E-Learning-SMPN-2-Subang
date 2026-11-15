<x-print-layout :title="'Rekap Nilai ' . ($selectedClassSubject?->schoolClass->name ?? '') . ' - ' . ($selectedClassSubject?->subject->name ?? '')">
    <div style="text-align: center; margin-bottom: 15px;">
        <h3 style="margin: 0; text-transform: uppercase;">DAFTAR REKAPITULASI NILAI AKADEMIK SISWA</h3>
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
            <td><strong>KKM Mata Pelajaran</strong></td>
            <td>: <strong>{{ $selectedClassSubject?->subject->kkm ?? '-' }}</strong></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">NIS</th>
                <th style="width: 28%;">Nama Lengkap Siswa</th>
                @foreach($components as $comp)
                    <th style="font-size: 8.5pt;">
                        {{ $comp->name }}<br>
                        ({{ $comp->weight }}%)
                    </th>
                @endforeach
                <th style="width: 12%;">Nilai Akhir</th>
                <th style="width: 8%;">Predikat</th>
                <th style="width: 12%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($gradesRecap as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="text-align: center; font-family: monospace;">{{ $item['student']->nis }}</td>
                    <td><strong>{{ $item['student']->user->name }}</strong></td>
                    @foreach($components as $comp)
                        <td style="text-align: center;">
                            {{ $item['scores'][$comp->id] !== null ? $item['scores'][$comp->id] : '-' }}
                        </td>
                    @endforeach
                    <td style="text-align: center; font-weight: bold;">{{ $item['finalScore'] }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $item['predicate'] }}</td>
                    <td style="text-align: center; font-size: 8.5pt;">{{ $item['isPassed'] ? 'Tuntas' : 'Remedial' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 6 + $components->count() }}" style="text-align: center; padding: 20px;">Belum ada data nilai siswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</x-print-layout>
