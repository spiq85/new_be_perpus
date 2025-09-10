<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Buku Populer</title>
    <style>
        body { font-family: DejaVu Sans; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h2>Laporan Buku Populer</h2>
    <p>Tanggal cetak: {{ now()->toDateString() }}</p>
    <table>
        <thead>
            <tr>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Rata-rata Rating</th>
                <th>Total Review</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($books as $book)
            <tr>
                <td>{{ $book->title }}</td>
                <td>{{ $book->author }}</td>
                <td>{{ number_format($book->reviews_avg_rating, 2) }}</td>
                <td>{{ $book->reviews_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
