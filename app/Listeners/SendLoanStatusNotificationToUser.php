<?php

namespace App\Listeners;

use App\Events\LoanStatusUpdated;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class SendLoanStatusNotificationToUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LoanStatusUpdated $event): void
    {
        $loan = $event->loan;

        try {
            $status = $loan->status_peminjaman;
            $bookTitle = $loan->book?->title ?? 'Buku';
            $userId = $loan->id_user;

            // Determine notification message and type based on status
            $notificationType = 'loan_status';
            $title = 'Status Peminjaman Updated';
            
            $message = match ($status) {
                'pending' => "Pengajuan peminjaman '{$bookTitle}' dikirim.",
                'siap_diambil' => "Buku '{$bookTitle}' siap diambil di perpustakaan.",
                'ditolak' => "Pengajuan peminjaman '{$bookTitle}' ditolak petugas.",
                'dipinjam' => "Buku '{$bookTitle}' berhasil kamu pinjam.",
                'menunggu_validasi_pengembalian' => "Permintaan pengembalian '{$bookTitle}' sedang menunggu validasi petugas.",
                'dikembalikan' => "Pengembalian '{$bookTitle}' selesai. Terima kasih 🙌",
                'rusak' => "Pengembalian '{$bookTitle}' ditandai RUSAK oleh petugas.",
                'hilang' => "Pengembalian '{$bookTitle}' ditandai HILANG oleh petugas.",
                default => "Status '{$bookTitle}' sekarang: {$status}",
            };

            // Create notification for user
            Notification::create([
                'user_id' => $userId,
                'type' => $notificationType,
                'title' => $title,
                'message' => $message,
                'data' => [
                    'loan_id' => $loan->id_loan,
                    'book_id' => $loan->id_book,
                    'status' => $status,
                ],
                'is_read' => false,
            ]);

            Log::info("Notifikasi status peminjaman untuk User {$loan->user->username}: {$message}");
        } catch (\Exception $e) {
            Log::error("Error saat create notifikasi loan status: " . $e->getMessage());
        }
    }
}
