<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    // Menambah atau Menghapus Buku dari Koleksi Pribadi
    public function toggle(Book $book)
    {
        $user = Auth::user();

        // Cek Apakah Buku Sudah ada di Koleksi
        $existingCollection = $user->collections()
        ->where('id_book', $book->id_book)
        ->first();

        if ($existingCollection) {
            // Hapus Jika Sudah Ada di Koleksi
            $existingCollection->delete();
            return response()->json([
                'message' => 'Buku Dihapus dari Koleksi'
            ]);
        } else {
            $collection = $user->collections()->create(['id_book' => $book->id_book]);
            return response()->json([
                'message' => 'Buku ditambahkan ke Koleksi.', 'data' => $collection
            ],201);
        }
    }

    public function index()
    {
        $collections = Collection::with(['book', 'book.categories'])
            ->where('id_user', Auth::id())
            ->latest()
            ->get();

        return response()->json(
            $collections->map(function($collection){
                $book = $collection->book;
                return [
                    'id_collection' => $collection->id_collection,
                    'book' => [
                        'id' => $book->id_book,
                        'title' => $book->title,
                        'author' => $book->author,
                        'publisher' => $book->publisher,
                        'publish_year' => $book->publish_year,
                        'description' => $book->description,
                        'stock' => $book->stock,
                        'category' => $book->categories->first()
                        ? [
                            'id' => $book->categories->first()->id_category,
                            'category_name' => $book->categories->first()->category_name,
                        ]
                        : null,
                        'cover' => $book->getFirstMediaUrl('cover'),
                    ],
                ];
            })
        );
    }
}
