<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Statistik Kategori Buku</title>
    <style>
        body { font-family: DejaVu Sans; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h2>Laporan Statistik Kategori Buku</h2>
    <p>Tanggal cetak: {{ now()->toDateString() }}</p>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Total Buku</th>
                <th>Total Dipinjam</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $cat)
            <tr>
                <td>{{ $cat['category_name'] }}</td>
                <td>{{ $cat['total_books'] }}</td>
                <td>{{ $cat['total_loans'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
