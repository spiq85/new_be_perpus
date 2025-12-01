<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Review;
use App\Models\Loan;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // Menyimpan ulasan & rating sebuah buku
    public function store(Request $request, Book $book,)
    {
        $user = Auth::user();

        $request->validate([
            'review' => 'required|string',
            'rating' => 'required|integer|between:1,5',
        ]);

        $hasReturned = $user->loans()
            ->where('id_book', $book->id_book)
            ->where('status_peminjaman', 'dikembalikan')
            ->exists();

            if(!$hasReturned) {
                return response()->json([
                    'message' => 'Anda hanya dapat memberikan ulasan setelah mengembalikan buku ini.'
                ], 422);
            }

        $review = Review::updateOrCreate(
            [
                'id_user' => Auth::id(),
                'id_book' => $book->id_book,
            ],
            [
                'review' => $request->review,
                'rating' => $request->rating,
            ]
        );
        return response()->json([
            'message' => 'Ulasan berhasil disimpan.' , 
            'data' => $review
        ],201);
    }

    // Menghapus Ulasan
    public function destroy(Review $review)
    {
        $user = Auth::user();
        if ($user->id_user !== $review->id_user && !$user->hasRole(['admin', 'petugas'])) {
            return response()->json([
               'message' => 'Anda Tidak Punya hak akses untuk menghapus ulasan ini' 
            ], 403);
        }

        $review->delete();
        return response()->json([
            'message' => 'Ulasan Berhasil Dihapus'
        ]);
    }
}
