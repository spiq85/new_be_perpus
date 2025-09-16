<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request\StoreLoanRequest;
use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Events\LoanRequested;

class LoanController extends Controller
{
    // User Melakukan Peminjaman Buku
    public function store(StoreLoanRequest $request)
    {
        $book = Book::find($request->id_book);

        if ($book->stock < 1) {
            return response([
                'message' => 'Stok Buku Tidak Tersedia'
            ], 422);
        }

        $loan = Loan::create([
            'id_user' => Auth::id(),
            'id_book' => $book->id_book,
            'tanggal_peminjaman' => null,
            'tanggal_pengembalian' => null,
            'status_peminjaman' => 'pending',
        ]);

        LoanRequested::dispatch($loan);

        return response()->json([
            'message' => 'Pengajuan peminjaman berhasil, tunggu validasi petugas.' , 'data' => $loan
        ],201);
    }

    // User Melihat Riwayat Peminjaman Buku
    public function myLoans(Request $request)
{
    $loans = Loan::where('id_user', Auth::id())
        ->with('book')
        ->latest();

    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $loans->whereHas('book', function ($q) use ($search) {
            $q->where('title', 'like', "%$search%");
        });
    }

    if ($request->has('status') && $request->status != 'all') {
        $loans->where('status_peminjaman', $request->status);
    }

    return response()->json(
        $loans->get()->map(function($loan){
            $book = $loan->book;
            return [
                'id_loan' => $loan->id_loan,
                'status' => $loan->status_peminjaman,
                'tanggal_peminjaman' => $loan->tanggal_peminjaman,
                'tanggal_pengembalian' => $loan->tanggal_pengembalian,
                'book' => [
                    'id' => $book->id_book,
                    'title' => $book->title,
                    'cover' => $book->getFirstMediaUrl('cover'),
                ],
            ];
        })
    );
}
    // Sisi Admin/Petugas Melihat Semua Data Peminjaman
    public function index(Request $request)
{
    $loans = Loan::with(['book', 'user'])->latest();

    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $loans->where(function($q) use ($search) {
            $q->whereHas('user', function($q2) use ($search){
                $q2->where('username', 'like', "%$search%")
                   ->orWhere('nama_lengkap', 'like', "%$search%");
            })->orWhereHas('book', function($q2) use ($search){
                $q2->where('title', 'like', "%$search%");
            });
        });
    }

    if ($request->has('status') && $request->status != 'all') {
        $loans->where("status_peminjaman", $request->status);
    }

    return response()->json($loans->get());
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
        'status_peminjaman' => 'siap_diambil',
      ]);
      
    //   UserHasBookReadyForPickup::dispatch($loan);
    return response()->json([
        'message' => 'Peminjaman berhasil divalidasi. Buku siap diambil.', 'data' => $loan
        ]);
    }

    public function rejectionLoan(Loan $loan)
    {
        if ($loan->status_peminjaman !== 'pending') {
            return response()->json([
                'message' => 'Peminjaman ini sudah diproses'
            ],422);
        }

        $loan->update([
            'status_peminjaman' => 'ditolak',
        ]);
        
        return response()->json([
            'message' => 'Peminjaman ditolak',
            'data' => $loan,
        ]);
    }

    public function pickupConfirmation(Loan $loan)
    {
        if ($loan->status_peminjaman !== 'siap_diambil') {
            return response()->json([
                'message' => 'Buku ini belum divalidasi atau sudah dipinjam'
            ],422);
        }

        $loan->book->decrement('stock');
        $loan->update([
            'status_peminjaman' => 'dipinjam',
            'tanggal_peminjaman' => now(),
            'due_date' => now()->addDays(7),
        ]);

        return response()->json([
            'message' => 'Buku berhasil diambil oleh peminjam', 'data' => $loan
        ]);
    }

    public function returnBook(Request $request,Loan $loan)
    {
       $request->validate([
        'condition' => 'required|string|in:baik,rusak,hilang',
       ]);

       if (in_array($loan->status_peminjaman, ['dikembalikan','hilang','rusak'])) {
        return response()->json([
            'message' => 'Buku ini statusnya sudah selesai diproses.'
        ],422);
       }

       $book = $loan->book;
       $newStatus = 'dikembalikan';

       switch ($request->condition) {
        case 'baik';
            $newStatus = 'dikembalikan';
            $book->increment('stock');
            break;
        case 'rusak';
            $newStatus = 'rusak';
            break;
        case 'hilang';
            $newStatus = 'hilang';
            break;
       }
       $loan->update([
        'tanggal_pengembalian' => now(),
        'status_peminjaman' => $newStatus
       ]);
       return response()->json([
        'message' => 'Buku berhasil diproses dengan status:' . $newStatus, 'data' => $loan
       ]);
    }
}
