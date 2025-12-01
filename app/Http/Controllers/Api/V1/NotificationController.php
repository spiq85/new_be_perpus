<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Loan;

class NotificationController extends Controller
{
    public function index()
    {
        $loans = Loan::where('id_user', Auth::id())
            ->with('book')
            ->orderByDesc('updated_at')
            ->take(20)
            ->get();

        $data = $loans->map(function ($loan) {
            $status = $loan->status_peminjaman;
            $title = $loan->book?->title ?? 'Buku';

            $message = match ($status) {
                'pending' => "Pengajuan peminjaman '{$title}' dikirim.",
                'siap_diambil' => "Buku '{$title}' siap diambil di perpustakaan.",
                'ditolak' => "Pengajuan peminjaman '{$title}' ditolak petugas.",
                'dipinjam' => "Buku '{$title}' berhasil kamu pinjam.",
                'menunggu_validasi_pengembalian' => "Permintaan pengembalian '{$title}' sedang menunggu validasi petugas.",
                'dikembalikan' => "Pengembalian '{$title}' selesai. Terima kasih 🙌",
                'rusak' => "Pengembalian '{$title}' ditandai RUSAK oleh petugas.",
                'hilang' => "Pengembalian '{$title}' ditandai HILANG oleh petugas.",
                default => "Status '{$title}' sekarang: {$status}",
            };

            return [
                'id_loan' => $loan->id_loan,
                'status' => $status,
                'book' => $title,
                'message' => $message,
                'updated_at' => $loan->updated_at,
            ];
        });

        return response()->json($data);
    }
}
