<x-print-layout :title="'Daftar Siswa Kelas ' . ($selectedClass?->name ?? '')">
    <div style="text-align: center; margin-bottom: 15px;">
        <h3 style="margin: 0; text-transform: uppercase;">DAFTAR SISWA ROMBONGAN BELAJAR</h3>
        <p style="margin: 5px 0; font-size: 11pt;">Tahun Ajaran {{ $selectedYear?->name ?? '-' }} (Semester {{ ucfirst($selectedYear?->semester ?? '-') }})</p>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 15%;"><strong>Kelas</strong></td>
            <td style="width: 35%;">: {{ $selectedClass?->name ?? '-' }} (Tingkat {{ $selectedClass?->grade_level ?? '-' }})</td>
            <td style="width: 15%;"><strong>Wali Kelas</strong></td>
            <td style="width: 35%;">: {{ $selectedClass?->homeroomTeacher?->user->name ?? 'Belum Ditentukan' }}</td>
        </tr>
        <tr>
            <td><strong>Jumlah Siswa</strong></td>
            <td>: {{ $enrollments->count() }} Orang</td>
            <td><strong>NIP Wali Kelas</strong></td>
            <td>: {{ $selectedClass?->homeroomTeacher?->nip ?? '-' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">NIS</th>
                <th style="width: 35%;">Nama Lengkap Siswa</th>
                <th style="width: 10%;">L/P</th>
                <th style="width: 15%;">Tanggal Lahir</th>
                <th style="width: 20%;">Alamat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enrollments as $index => $enr)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="text-align: center; font-family: monospace;">{{ $enr->student->nis }}</td>
                    <td><strong>{{ $enr->student->user->name }}</strong></td>
                    <td style="text-align: center;">{{ $enr->student->gender === 'male' ? 'L' : 'P' }}</td>
                    <td style="text-align: center;">{{ $enr->student->birth_date?->format('d/m/Y') ?? '-' }}</td>
                    <td style="font-size: 8.5pt;">{{ $enr->student->address ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">Tidak ada data siswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</x-print-layout>
