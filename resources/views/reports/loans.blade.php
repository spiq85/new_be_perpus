<h1>Laporan Peminjaman ({{ $type }})</h1>
<p>Tanggal cetak: {{ $date }}</p>

<table border="1" cellspacing="0" cellpadding="6" width="100%">
    <thead>
        <tr>
            <th>User</th>
            <th>Buku</th>
            <th>Tanggal Pinjam</th>
            <th>Jatuh Tempo</th>
            <th>Status</th>
            <th>Denda</th>
        </tr>
    </thead>
    <tbody>
        @foreach($loans as $loan)
        <tr>
            <td>{{ $loan->user->username }}</td>
            <td>{{ $loan->book->title }}</td>
            <td>{{ $loan->tanggal_peminjaman }}</td>
            <td>{{ $loan->due_date }}</td>
            <td>{{ $loan->status_peminjaman }}</td>
            <td>Rp {{ number_format($loan->denda,0,',','.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
