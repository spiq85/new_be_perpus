@extends('layouts.pdf', [
    'title' => 'Laporan Inventori Buku',
    'subtitle' => ucfirst($status ?? 'Semua Buku')
])

@section('content')
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Judul Buku</th>
            <th>Penulis</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th>Total Dipinjam</th>
        </tr>
    </thead>
    <tbody>
        @forelse($books as $i => $book)
        <tr>
            <td class="text-center font-bold">{{ $i + 1 }}</td>
            <td class="font-bold">{{ $book->title }}</td>
            <td>{{ $book->author ?? '-' }}</td>
            <td>
                @if($book->categories->isNotEmpty())
                    {{ $book->categories->pluck('category_name')->join(', ') }}
                @else
                    <em>-</em>
                @endif
            </td>
            <td class="text-center font-bold {{ $book->stock == 0 ? 'text-red-600' : 'text-green-600' }}">
                {{ $book->stock }}
            </td>
            <td class="text-center font-bold text-indigo-600">{{ $book->loans_count }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">Tidak ada data buku</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection