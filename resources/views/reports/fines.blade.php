<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Denda - {{ $type }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        h2, h4 {
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }
        th {
            background: #f2f2f2;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2 class="text-center">Laporan Denda</h2>
    <h4 class="text-center">Tipe: {{ $type }}</h4>
    <p class="text-center">Tanggal Cetak: {{ $date }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Judul Buku</th>
                <th>Status</th>
                <th>Denda</th>
                <th>Tanggal Update</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $i => $loan)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $loan->user->nama_lengkap ?? $loan->user->username ?? '-' }}</td>
                    <td>{{ $loan->book->title ?? '-' }}</td>
                    <td>{{ ucfirst($loan->status_peminjaman) }}</td>
                    <td>Rp {{ number_format($loan->denda, 0, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($loan->updated_at)->format('d-m-Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data denda</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
