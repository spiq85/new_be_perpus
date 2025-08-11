<?php

namespace App\Listeners;

use App\Events\LoanRequested;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

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

        Log::info("Notifikasi untuk Petugas : User '{$user->username}' mengajukan peminjaman buku '{$book->title}'.");
    }
}
