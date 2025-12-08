@extends('layouts.pdf', ['title' => 'Statistik Kategori Buku'])

@section('content')
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Kategori</th>
            <th>Total Buku</th>
            <th>Total Dipinjam</th>
            <th>Persentase Pinjam</th>
        </tr>
    </thead>
    <tbody>
        @php $totalAll = $categories->sum('total_loans') @endphp
        @foreach($categories as $i => $cat)
        <tr>
            <td class="text-center font-bold">{{ $i + 1 }}</td>
            <td class="font-bold">{{ $cat['category_name'] }}</td>
            <td class="text-center">{{ $cat['total_books'] }}</td>
            <td class="text-center font-bold text-indigo-600">{{ $cat['total_loans'] }}</td>
            <td class="text-center">
                @if($totalAll > 0)
                    <strong>{{ number_format(($cat['total_loans'] / $totalAll) * 100, 1) }}%</strong>
                @else 0% @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection