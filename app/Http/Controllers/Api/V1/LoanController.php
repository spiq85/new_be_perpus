<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Loan;
use App\Models\Book;
use App\Models\Review;
use App\Events\LoanRequested;
use App\Events\LoanStatusUpdated;

class LoanController extends Controller
{
    // =========================
    // USER AJUKAN PEMINJAMAN
    // =========================
    public function store(StoreLoanRequest $request)
    {
        $book = Book::find($request->id_book);
        if (!$book) {
            return response()->json(['message' => 'Buku tidak ditemukan.'], 404);
        }
        if ($book->stock < 1) {
            return response()->json(['message' => 'Stok buku tidak tersedia'], 422);
        }

        $loan = Loan::create([
            'id_user'                     => Auth::id(),
            'id_book'                     => $book->id_book,
            'tanggal_peminjaman'          => null,
            'tanggal_pengembalian'        => null,
            'due_date'                    => null,
            'status_peminjaman'           => 'pending',
            'denda'                       => 0,
            'requested_return_condition'  => null,
            'return_note'                 => null,
        ])->load(['user', 'book']);

        LoanRequested::dispatch($loan);

        return response()->json([
            'message' => 'Pengajuan peminjaman berhasil, tunggu validasi petugas.',
            'data'    => $loan,
        ], 201);
    }

    // =========================
    // USER LIHAT RIWAYATNYA + REVIEW SUDAH IKUT!
    // =========================
    public function myLoans(Request $request)
    {
        $loans = Loan::where('id_user', Auth::id())
            ->with(['book', 'user'])
            ->latest()
            ->get();

        // load review manual karena eager-load hasOne dengan 2 FK tidak bisa
        foreach ($loans as $loan) {
            $loan->review = Review::where('id_user', $loan->id_user)
                ->where('id_book', $loan->id_book)
                ->first();
        }

        return response()->json(
            $loans->map(function ($loan) {
                return [
                    'id_loan' => $loan->id_loan,
                    'status' => $loan->status_peminjaman,
                    'tanggal_peminjaman' => $loan->tanggal_peminjaman,
                    'tanggal_pengembalian' => $loan->tanggal_pengembalian,
                    'due_date' => $loan->due_date,

                    'book' => [
                        'id_book' => $loan->book->id_book,
                        'title'   => $loan->book->title,
                        'author'  => $loan->book->author,
                        'cover'   => $loan->book->getFirstMediaUrl('cover'),
                    ],

                    'review' => $loan->review ? [
                        'id_review' => $loan->review->id_review,
                        'rating'    => $loan->review->rating,
                        'review'    => $loan->review->review,
                    ] : null,
                ];
            })
        );
    }

    // =========================
    // PETUGAS / ADMIN LIHAT SEMUA LOAN
    // =========================
    public function index(Request $request)
    {
        $loans = Loan::with(['book', 'user', 'review'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $loans->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q2) use ($search) {
                    $q2->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_lengkap', 'like', "%{$search}%");
                })->orWhereHas('book', function ($q2) use ($search) {
                    $q2->where('title', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $loans->where('status_peminjaman', $request->status);
        }

        return response()->json(
            $loans->get()->map(function ($loan) {
                return [
                    'id_loan'                     => $loan->id_loan,
                    'status_peminjaman'           => $loan->status_peminjaman,
                    'tanggal_peminjaman'          => $loan->tanggal_peminjaman,
                    'due_date'                    => $loan->due_date,
                    'tanggal_pengembalian'        => $loan->tanggal_pengembalian,
                    'denda'                       => $loan->denda,
                    'requested_return_condition'  => $loan->requested_return_condition,
                    'return_note'                 => $loan->return_note,
                    'book' => [
                        'id_book' => $loan->book->id_book,
                        'title'   => $loan->book->title,
                        'author'  => $loan->book->author ?? 'Tidak diketahui',
                        'cover'   => $loan->book->getFirstMediaUrl('cover')
                            ?? "https://via.placeholder.com/200x300?text=No+Cover",
                    ],
                    'user' => [
                        'id_user'      => $loan->user->id_user,
                        'username'     => $loan->user->username,
                        'nama_lengkap' => $loan->user->nama_lengkap,
                    ],
                    'review' => $loan->review ? [
                        'id_review' => $loan->review->id_review,
                        'rating'    => (int) $loan->review->rating,
                        'review'    => $loan->review->review,
                    ] : null,
                ];
            })
        );
    }

    // =========================
    // PETUGAS VALIDASI PENGAJUAN PINJAM
    // =========================
    public function validateLoan(Loan $loan, Request $request)
    {
        $request->validate([
            'status' => 'required|string|in:siap_diambil,ditolak',
        ]);

        if ($loan->status_peminjaman !== 'pending') {
            return response()->json(['message' => 'Peminjaman ini sudah diproses'], 422);
        }

        $loan->update(['status_peminjaman' => $request->status]);
        $loan->load(['book', 'user']);
        LoanStatusUpdated::dispatch($loan);

        return response()->json([
            'message' => "Peminjaman berhasil diubah ke status {$request->status}",
            'data'    => $loan,
        ]);
    }

    // =========================
    // USER KONFIRMASI AMBIL BUKU
    // =========================
    public function pickupConfirmation(Loan $loan)
    {
        if ($loan->id_user !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak punya akses untuk loan ini.'], 403);
        }

        if ($loan->status_peminjaman !== 'siap_diambil') {
            return response()->json(['message' => 'Buku ini belum siap diambil atau sudah dipinjam.'], 422);
        }

        DB::beginTransaction();
        try {
            $book = Book::where('id_book', $loan->id_book)->lockForUpdate()->first();

            if (!$book || $book->stock < 1) {
                DB::rollBack();
                return response()->json(['message' => 'Stok buku tidak tersedia.'], 422);
            }

            $loan->update([
                'status_peminjaman'   => 'dipinjam',
                'tanggal_peminjaman'  => now(),
                'due_date'            => now()->addDays(7),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memproses pengambilan buku.'], 500);
        }

        $loan->load(['book', 'user']);
        LoanStatusUpdated::dispatch($loan);

        return response()->json([
            'message' => 'Buku berhasil diambil. Status sekarang DIPINJAM.',
            'data'    => $loan,
        ]);
    }

    // =========================
    // USER AJUKAN PENGEMBALIAN
    // =========================
    public function requestReturn(Request $request, Loan $loan)
    {
        $request->validate([
            'condition' => 'required|string|in:baik,rusak,hilang',
            'note'      => 'nullable|string',
        ]);

        if ($loan->id_user !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak punya akses untuk loan ini.'], 403);
        }

        if ($loan->status_peminjaman !== 'dipinjam') {
            return response()->json(['message' => 'Pengembalian tidak bisa diajukan untuk status ini.'], 422);
        }

        $loan->update([
            'requested_return_condition' => $request->condition,
            'return_note'                => $request->note,
            'status_peminjaman'          => 'menunggu_validasi_pengembalian',
        ]);

        $loan->load(['book', 'user']);
        LoanStatusUpdated::dispatch($loan);

        return response()->json([
            'message' => 'Permintaan pengembalian dikirim, menunggu validasi petugas.',
            'data'    => $loan,
        ]);
    }

    // =========================
    // PETUGAS FINALISASI PENGEMBALIAN
    // =========================
    public function finalizeReturn(Request $request, Loan $loan)
    {
        $request->validate([
            'condition' => 'required|string|in:baik,rusak,hilang',
        ]);

        if ($loan->status_peminjaman !== 'menunggu_validasi_pengembalian') {
            return response()->json(['message' => 'Loan ini belum minta pengembalian / sudah diproses.'], 422);
        }

        $finalStatus = match ($request->condition) {
            'rusak'  => 'rusak',
            'hilang' => 'hilang',
            default  => 'dikembalikan',
        };

        $loan->update([
            'status_peminjaman'    => $finalStatus,
            'tanggal_pengembalian' => now(),
        ]);

        $loan->load(['book', 'user']);
        LoanStatusUpdated::dispatch($loan);

        return response()->json([
            'message' => "Buku diproses, status akhir: {$finalStatus}. Anda bisa memberikan rating dan ulasan.",
            'data'    => $loan,
        ]);
    }
}
