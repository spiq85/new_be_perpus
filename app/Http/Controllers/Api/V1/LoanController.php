<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LoanController extends Controller
{
    // User Melakukan Peminjaman Buku
    public function store(Request $request)
    {
        $request->validate([
            'id_book' => 'required|exists:books,id_book',
        ]);

        $book = Book::find($request->id_book);

        if ($book->stock < 1) {
            return response([
                'message' => 'Stok Buku Tidak Tersedia'
            ], 422);
        }

        $loan = Loan::create([
            'id_user' => Auth::id(),
            'id_book' => $book->id_book,
            'tanggal_peminjaman' => now(),
            'tanggal_pengembalian' => null,
            'status_peminjaman' => 'pending',
        ]);

        return response()->json([
            'message' => 'Pengajuan peminjaman berhasil, tunggu validasi petugas.' , 'data' => $loan
        ],201);
    }

    // User Melihat Riwayat Peminjaman Buku
    public function myLoans()
    {
        $loans = Loan::where('id_user', Auth::id())
            ->with('book')
            ->latest()
            ->get();

            return response()->json($loans);
    }

    // Sisi Admin/Petugas Melihat Semua Data Peminjaman
    public function index()
    {
        $loans = Loan::with(['book', 'user'])->latest()->paginate(20);
        return response()->json($loans);
    }

    // Petugas/Admin Update Status Peminjaman
    public function validateLoan(Loan $loan)
    {
      if ($loan->status_peminjaman !== 'pending'){
        return response()->json([
            'message' => 'Peminjaman ini sudah divalidasi atau selesai.'
        ],422);
      }


      $loan->update([
        'status_peminjaman' => 'dipinjam',
        'tanggal_peminajaman' => now(),
        'tanggal_pengembalian' => now()->addDays(7),
      ]);
      return response()->json([
        'message' => 'Peminjaman berhasil divalidasi.' , 'data' => $loan
      ]);
    }

    public function returnBook(Loan $Loan)
    {
        if ($loan->status_peminajaman === 'dikembalikan') {
            return response()->json([
                'message' => 'Buku ini sudah dikembalikan sebelumnya'
            ],422);
        }

        $loan->update([
            'tanggal_pengembalian' => now(),
            'status_peminjaman' => 'dikembalikan'
        ]);


        return response()->json([
            'message' => 'Buku berhasil dikembalikan.', 'data' => $Loan
        ]);
    }
}
