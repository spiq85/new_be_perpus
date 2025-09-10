<h1>Laporan Buku ({{ ucfirst($status ?? 'Semua') }})</h1>
<p>Tanggal cetak: {{ $date }}</p>

<table border="1" cellspacing="0" cellpadding="6" width="100%">
    <thead>
        <tr>
            <th>Judul</th>
            <th>Penulis</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th>Total Dipinjam</th>
        </tr>
    </thead>
    <tbody>
        @foreach($books as $book)
        <tr>
            <td>{{ $book->title }}</td>
            <td>{{ $book->author }}</td>
            <td>
                @if($book->categories->isNotEmpty())
                    {{ $book->categories->pluck('category_name')->join(', ') }}
                @else
                    -
                @endif
            </td>
            <td>{{ $book->stock }}</td>
            <td>{{ $book->loans_count }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
