<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loan;
use Carbon\Carbon;

class CheckOverdueLoans extends Command
{
    protected $signature = 'app:check-overdue-loans';
    protected $description = 'Cek peminjaman yang telat dan update status serta denda';

    public function handle()
    {
        $this->info('Mulai pengecekan buku yang terlambat...');

        $overdueLoans= Loan::where('status_peminjaman', 'dipinjam')
                                ->where('due_date', '<', Carbon::today())
                                ->get();

        foreach ($overdueLoans as $loan) {
            $overdueDays = Carbon::today()->diffInDays($loan->due_date);

            $fine = $overdueDays * 500;

            $loan->update([
                'status_peminjaman' => 'terlambat',
                'denda' => $fine,
            ]);
            $this->info("User #{$loan->id_user} yelat mengembalikan buku #{$loan->id_book}. Denda: #{$fine}");
        }
        $this->info('Pengecekan Selesai.');
    }
}
