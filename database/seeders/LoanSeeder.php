<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\loan;
use App\Models\Book;
use App\Models\User;
use Carbon\Carbon;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::find(3);

        if (!$user) {
            $this->command->error('User dengan id 3 tidak ditemukan!');
            return;
        }

        $books = Book::inRandomOrder()->take(12)->get();

        if ($books->count() < 12) {
            $this->command->error('Tidak cukup buku! Butuh minimal 12 buku.');
            return;
        }

        $now = Carbon::now();

        // 1. Pending (baru diajukan)
        Loan::create([
            'id_user' => 3,
            'id_book' => $books[0]->id_book,
            'status_peminjaman' => 'pending',
            'denda' => 0,
        ]);

        // 2. Ditolak
        Loan::create([
            'id_user' => 3,
            'id_book' => $books[1]->id_book,
            'status_peminjaman' => 'ditolak',
            'denda' => 0,
        ]);

        // 3. Siap diambil
        Loan::create([
            'id_user' => 3,
            'id_book' => $books[2]->id_book,
            'status_peminjaman' => 'siap_diambil',
            'denda' => 0,
        ]);

        // 4. Sedang dipinjam (masih dalam masa pinjam)
        Loan::create([
            'id_user' => 3,
            'id_book' => $books[3]->id_book,
            'tanggal_peminjaman' => $now->copy()->subDays(3),
            'due_date' => $now->copy()->addDays(4),
            'status_peminjaman' => 'dipinjam',
            'denda' => 0,
        ]);

        // 5. Sedang dipinjam + TELAT (akan jadi terlambat otomatis oleh command)
        Loan::create([
            'id_user' => 3,
            'id_book' => $books[4]->id_book,
            'tanggal_peminjaman' => $now->copy()->subDays(10),
            'due_date' => $now->copy()->subDays(3), // sudah telat 3 hari
            'status_peminjaman' => 'dipinjam',
            'denda' => 0, // command akan update jadi 15.000
        ]);

        // 6. Menunggu validasi pengembalian
        Loan::create([
            'id_user' => 3,
            'id_book' => $books[5]->id_book,
            'tanggal_peminjaman' => $now->copy()->subDays(8),
            'due_date' => $now->copy()->subDays(1),
            'status_peminjaman' => 'menunggu_validasi_pengembalian',
            'requested_return_condition' => 'baik',
            'return_note' => 'Buku sudah dikembalikan dalam kondisi baik.',
            'denda' => 0,
        ]);

        // 7. Dikembalikan tepat waktu
        Loan::create([
            'id_user' => 3,
            'id_book' => $books[6]->id_book,
            'tanggal_peminjaman' => $now->copy()->subDays(14),
            'due_date' => $now->copy()->subDays(7),
            'tanggal_pengembalian' => $now->copy()->subDays(8),
            'status_peminjaman' => 'dikembalikan',
            'denda' => 0,
        ]);

        // 8. Dikembalikan + telat 5 hari → denda Rp 25.000
        Loan::create([
            'id_user' => 3,
            'id_book' => $books[7]->id_book,
            'tanggal_peminjaman' => $now->copy()->subDays(20),
            'due_date' => $now->copy()->subDays(13),
            'tanggal_pengembalian' => $now->copy()->subDays(8),
            'status_peminjaman' => 'dikembalikan',
            'denda' => 70000, // 5 hari × 5000
        ]);

        // 9. Rusak → denda Rp 25.000
        Loan::create([
            'id_user' => 3,
            'id_book' => $books[8]->id_book,
            'tanggal_peminjaman' => $now->copy()->subDays(12),
            'due_date' => $now->copy()->subDays(5),
            'tanggal_pengembalian' => $now->copy()->subDays(2),
            'status_peminjaman' => 'rusak',
            'requested_return_condition' => 'rusak',
            'denda' => 55000,
        ]);

        // 10. Rusak + telat 4 hari → denda Rp 45.000
        Loan::create([
            'id_user' => 3,
            'id_book' => $books[9]->id_book,
            'tanggal_peminjaman' => $now->copy()->subDays(18),
            'due_date' => $now->copy()->subDays(11),
            'tanggal_pengembalian' => $now->copy()->subDays(7),
            'status_peminjaman' => 'rusak',
            'requested_return_condition' => 'rusak',
            'denda' => 85000, // 4 hari telat (20.000) + rusak (25.000)
        ]);

        // 11. Hilang → denda Rp 50.000
        Loan::create([
            'id_user' => 3,
            'id_book' => $books[10]->id_book,
            'tanggal_peminjaman' => $now->copy()->subDays(25),
            'due_date' => $now->copy()->subDays(18),
            'tanggal_pengembalian' => $now->copy()->subDays(10),
            'status_peminjaman' => 'hilang',
            'requested_return_condition' => 'hilang',
            'denda' => 50000,
        ]);

        // 12. Hilang + telat 10 hari → denda Rp 100.000
        Loan::create([
            'id_user' => 3,
            'id_book' => $books[11]->id_book,
            'tanggal_peminjaman' => $now->copy()->subDays(40),
            'due_date' => $now->copy()->subDays(33),
            'tanggal_pengembalian' => $now->copy()->subDays(20),
            'status_peminjaman' => 'hilang',
            'requested_return_condition' => 'hilang',
            'denda' => 220000, // 10 hari telat (50.000) + hilang (50.000)
        ]);

        $this->command->info("Berhasil buat 12 loan untuk user ID 3 dengan berbagai status & denda!");
        $this->command->info("Jangan lupa jalankan: php artisan app:check-overdue-loans untuk update status 'terlambat' otomatis!");
    }
}
