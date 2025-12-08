@extends('layouts.pdf', ['title' => '10 Buku Paling Populer'])

@section('content')
<table>
    <thead>
        <tr>
            <th>Rank</th>
            <th>Judul Buku</th>
            <th>Penulis</th>
            <th>Rata-rata Rating</th>
            <th>Total Ulasan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($books as $i => $book)
        <tr>
            <td class="text-center font-bold text-2xl text-indigo-600">#{{ $i + 1 }}</td>
            <td class="font-bold">{{ $book->title }}</td>
            <td>{{ $book->author ?? '-' }}</td>
            <td class="text-center font-bold text-yellow-600">
                {{ number_format($book->reviews_avg_rating, 1) }} / 5.0
            </td>
            <td class="text-center font-bold text-indigo-600">
                {{ $book->reviews_count }} ulasan
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection