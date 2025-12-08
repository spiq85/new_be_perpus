@extends('layouts.pdf', [
    'title' => 'Laporan Denda',
    'subtitle' => "Tipe: {$type}"
])

@section('content')
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Peminjam</th>
            <th>Buku</th>
            <th>Status</th>
            <th>Denda</th>
            <th>Tanggal Update</th>
        </tr>
    </thead>
    <tbody>
        @forelse($loans as $i => $loan)
        <tr>
            <td class="text-center font-bold">{{ $i + 1 }}</td>
            <td>{{ $loan->user->nama_lengkap ?? $loan->user->username }}</td>
            <td class="font-bold">{{ $loan->book->title }}</td>
            <td>
                <span class="badge badge-danger">
                    {{ ucfirst($loan->status_peminjaman) }}
                </span>
            </td>
            <td class="text-right font-bold text-red-600">
                Rp {{ number_format($loan->denda, 0, ',', '.') }}
            </td>
            <td>{{ $loan->updated_at->format('d/m/Y') }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center">Tidak ada data denda</td></tr>
        @endforelse
    </tbody>
</table>
@endsection