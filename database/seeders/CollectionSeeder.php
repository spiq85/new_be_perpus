<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;
use App\Models\Collection;

class CollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $books = Book::all();

        foreach ($users as $user) {
            $booksToCollect = $books->random(rand(5,15));

            foreach ($booksToCollect as $book) {
                Collection::firstOrCreate([
                    'id_user' => $user->id_user,
                    'id_book' => $book->id_book,
                ]);
            }
        }
    }
}
