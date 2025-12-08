<?php

namespace App\Listeners;

use App\Events\LoanStatusUpdated;
use App\Models\Notification;
use App\Models\User;

class SendReturnRequestNotificationToStaff
{
    public function handle(LoanStatusUpdated $event): void
    {
        $loan = $event->loan;

        // Hanya kirim notif ke petugas kalau statusnya jadi "menunggu_validasi_pengembalian"
        if ($loan->status_peminjaman !== 'menunggu_validasi_pengembalian') {
            return;
        }

        $user = $loan->user;
        $book = $loan->book;
        $condition = $loan->requested_return_condition ?? 'baik';
        $note = $loan->return_note ? " (Catatan: {$loan->return_note})" : '';

        $staffUsers = User::where('role', 'petugas')->get();

        foreach ($staffUsers as $staff) {
            Notification::create([
                'id_user' => $staff->id_user,
                'title'   => 'Pengajuan Pengembalian Buku',
                'message'  => "User '{$user->username}' mengajukan pengembalian buku '{$book->title}' [Kondisi: {$condition}]{$note}",
                'type'     => 'return_request',
                'is_read'  => false,
            ]);
        }
    }
}