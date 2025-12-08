<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loan;
use Carbon\Carbon;

class CheckOverdueLoans extends Command
{
    protected $signature = 'app:check-overdue-loans';
    protected $description = 'Cek peminjaman yang telat/rusak/hilang dan update status serta denda';

    public function handle()
    {
        $this->info('Memulai pengecekan peminjaman...');

        $today = Carbon::today();

        $loans = Loan::whereIn('status_peminjaman',['dipinjam', 'terlambat', 'hilang', 'rusak'])
            ->where('due_date', '<', $today)
            ->get();

        foreach ($loans as $loan) {
            $overdueDays = $today->diffInDays(Carbon::parse($loan->due_date));
            $finePerDay = 5000; // Denda Jika Telat Per Hari
            $fine = $overdueDays * $finePerDay;

            if ($loan->status_peminjaman === 'hilang') {
                $fine += 50000; // Denda Tambahan Jika Buku Hilang
            } elseif ($loan->status_peminjaman === 'rusak') {
                $fine += 25000; // Denda Tambahan Jika Buku Rusak
            }

            if ($loan->status_peminjaman === 'dipinjam' && $overdueDays > 0){
                $loan->status_peminjaman = 'terlambat';
            }

            $loan->denda = $fine;
            $loan->save();

            $this->info("User #{$loan->user->username} #{$loan->status_peminjaman} #{$loan->book->title}. Denda: Rp {$fine}");
        }
        $this->info("Pengecekan Selesai."); 
    }
}
