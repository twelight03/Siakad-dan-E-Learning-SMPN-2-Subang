<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Laporan Akademik' }} - SMP Negeri 2 Subang</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            background: #fff;
            margin: 0;
            padding: 20px;
            font-size: 12pt;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h3 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 2px 0;
            font-size: 16pt;
            font-weight: 900;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            font-size: 9pt;
            color: #333;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10pt;
        }
        .meta-table td {
            padding: 3px 5px;
            vertical-align: top;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9.5pt;
        }
        .data-table th, .data-table td {
            border: 1px solid #333;
            padding: 6px 8px;
        }
        .data-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .signature-block {
            margin-top: 40px;
            width: 100%;
        }
        .signature-block td {
            width: 50%;
            text-align: center;
            font-size: 10pt;
            vertical-align: top;
        }
        .no-print {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            background: #2563eb;
            color: #fff;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 10pt;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }
        .btn-secondary {
            background: #64748b;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <div>
            <strong>Mode Cetak Laporan Resmi:</strong> Pastikan printer diatur ke ukuran A4 atau simpan sebagai PDF.
        </div>
        <div>
            <button onclick="window.print()" class="btn">🖨️ Cetak / Simpan PDF</button>
            <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
        </div>
    </div>

    <!-- Kop Surat Resmi -->
    <div class="header">
        <h3>Pemerintah Daerah Kabupaten Subang</h3>
        <h3>Dinas Pendidikan dan Kebudayaan</h3>
        <h2>SMP Negeri 2 Subang</h2>
        <p>Jl. Otto Iskandardinata No. 115, Karanganyar, Kec. Subang, Kabupaten Subang, Jawa Barat 41211</p>
        <p>Email: smpn2subang@disdik.subang.go.id | NPSN: 20233000</p>
    </div>

    {{ $slot }}

    <!-- Titimangsa & Tanda Tangan -->
    <table class="signature-block">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala SMP Negeri 2 Subang<br><br><br><br><br>
                <strong><u>Drs. H. Suwandi, M.Pd.</u></strong><br>
                NIP. 19680512 199412 1 002
            </td>
            <td>
                Subang, {{ date('d F Y') }}<br>
                Petugas / Administrator SIAKAD<br><br><br><br><br>
                <strong><u>{{ auth()->user()->name }}</u></strong><br>
                NIP. {{ auth()->user()->teacher?->nip ?? '' }}
            </td>
        </tr>
    </table>
</body>
</html>
