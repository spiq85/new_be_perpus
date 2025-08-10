<?php

namespace App\Observers;

use App\Models\Loan;

class LoanObserver
{
    /**
     * Handle the Loan "created" event.
     */
    public function created(Loan $loan): void
    {
        //
    }

    /**
     * Handle the Loan "updated" event.
     */
    public function updated(Loan $loan): void
    {
        // Cek apakah kolom 'status_peminjaman' yang berubah
        if ($loan->isDirty('status_peminjaman')) {
            // Jika status berubah jadi 'dipinjam' (artinya divalidasi petugas)
            if ($loan->status_peminjaman === 'dipinjam') {
                $loan->book->decrement('stock');
            }
            // Jika status berubah jadi 'dikembalikan'
            elseif ($loan->status_peminjaman === 'dikembalikan') {
                $loan->book->increment('stock');
            }
        }
    }

    /**
     * Handle the Loan "deleted" event.
     */
    public function deleted(Loan $loan): void
    {
        //
    }

    /**
     * Handle the Loan "restored" event.
     */
    public function restored(Loan $loan): void
    {
        //
    }

    /**
     * Handle the Loan "force deleted" event.
     */
    public function forceDeleted(Loan $loan): void
    {
        //
    }
}
