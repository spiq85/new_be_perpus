<?php

namespace App\Listeners;

use App\Events\LoanRequested;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Notification;

class SendLoanNotificationToStaff
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

    public function handle(LoanRequested $event): void
    {
        $loan = $event->loan;
        $user = $loan->user;
        $book = $loan->book;

        $staffusers = User::where('role', 'petugas')->get();

        foreach ($staffusers as $staff) {
            Notification::create([
                'id_user' => $staff->id_user,
                'title' => 'Pengajuan Peminjaman Baru',
                'message' => "User '{$user->username}' mengajukan peminjaman buku '{$book->title}'",
                'type' => 'loan_request',
                'is_read' => false,
            ]);
        }

        Log::info("Notifikasi untuk Petugas : User '{$user->username}' mengajukan peminjaman buku '{$book->title}'.");
    }
}
