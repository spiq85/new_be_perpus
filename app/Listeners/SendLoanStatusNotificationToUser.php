<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\LoanStatusUpdated;
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
        Log::info("Notif untuk User {$loan->user->username} : status peminjman '{$loan->book->title} berubah jadi {$loan->status_peminjaman}");
    }
}
