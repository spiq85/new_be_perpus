<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Review;
use App\Models\Loan;
use Illuminate\Support\Facades\Auth;
use App\Events\LoanStatusUpdated;

class ReviewController extends Controller
{
    // Menyimpan ulasan & rating sebuah buku
    public function store(Request $request, Book $book)
    {
        $user = Auth::user();

        $request->validate([
            'review' => 'nullable|string|max:1000',  // gw ubah jadi nullable biar optional
            'rating' => 'required|integer|between:1,5',
        ]);

        $hasReturned = $user->loans()
            ->where('id_book', $book->id_book)
            ->where('status_peminjaman', 'dikembalikan')
            ->exists();

        if (!$hasReturned) {
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

        // INI YANG BIKIN SEMUA LANGSUNG MUNCUL DI TAB ULASAN!!!
        $loan = Loan::where('id_user', Auth::id())
            ->where('id_book', $book->id_book)
            ->where('status_peminjaman', 'dikembalikan')
            ->first();

        if ($loan) {
            $loan->load('review'); // ← INI YANG PENTING BANGET!
            LoanStatusUpdated::dispatch($loan); // ← trigger notifikasi + refresh frontend
        }
        // SAMPE SINI

        return response()->json([
            'message' => 'Ulasan berhasil disimpan.',
            'data'    => $review
        ], 201);
    }
    // Menghapus Ulasan
    public function destroy(Review $review)
    {
        $user = Auth::user();
        // User hanya bisa hapus review miliknya sendiri
        // Admin/Petugas bisa hapus review siapa saja
        if ($user->id_user !== $review->id_user && !$user->hasRole(['admin', 'petugas'])) {
            return response()->json([
                'message' => 'Anda Tidak Punya hak akses untuk menghapus ulasan ini'
            ], 403);
        }

        $review->delete();
        return response()->json([
            'message' => 'Ulasan Berhasil Dihapus'
        ], 200);
    }
}
