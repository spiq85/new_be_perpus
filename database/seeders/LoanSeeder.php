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
        $users = User::where('role', 'user')->get();
        $books = Book::all();

        for ($i = 0; $i < 20; $i++) {
            $user = $users->random();
            $book = $books->random();

            $isAlreadyBorrowing = Loan::where('id_user', $user->id_user)
                                        ->where('id_book', $book->id_book)
                                        ->where('status_peminjaman', 'dipinjam')
                                        ->exists();

            if ($book->stock > 0 && !$isAlreadyBorrowing) {
                $loanDate = Carbon::now()->subDays(rand(1,30));

                Loan::create([
                    'id_user' => $user->id_user,
                    'id_book' => $book->id_book,
                    'tanggal_peminjaman' => $loanDate,
                    'due_date' => $loanDate->copy()->addDays(7),
                    'status_peminjaman' => 'dipinjam',
                ]);
                $book->decrement('stock');
            }
        }
    }
}
