@extends('layouts.pdf', [
    'title' => 'Laporan Peminjaman Buku',
    'subtitle' => "Periode: {$type}"
])

@section('content')
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Peminjam</th>
            <th>Buku</th>
            <th>Tanggal Pinjam</th>
            <th>Jatuh Tempo</th>
            <th>Status</th>
            <th>Denda</th>
        </tr>
    </thead>
    <tbody>
        @forelse($loans as $i => $loan)
        <tr>
            <td class="text-center font-bold">{{ $i + 1 }}</td>
            <td>{{ $loan->user->nama_lengkap ?? $loan->user->username }}</td>
            <td class="font-bold">{{ $loan->book->title }}</td>
            <td>{{ $loan->tanggal_peminjaman?->format('d/m/Y') ?? '-' }}</td>
            <td>{{ $loan->due_date?->format('d/m/Y') ?? '-' }}</td>
            <td>
                <span class="badge 
                    @if($loan->status_peminjaman === 'dikembalikan') badge-success
                    @elseif(in_array($loan->status_peminjaman, ['terlambat','rusak','hilang'])) badge-danger
                    @else badge-warning @endif">
                    {{ ucfirst(str_replace('_', ' ', $loan->status_peminjaman)) }}
                </span>
            </td>
            <td class="text-right font-bold text-red-600">
                @if($loan->denda > 0) Rp {{ number_format($loan->denda, 0, ',', '.') }} @else - @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center">Tidak ada data peminjaman</td></tr>
        @endforelse
    </tbody>
</table>
@endsection