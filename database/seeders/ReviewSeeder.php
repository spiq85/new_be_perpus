<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $books = Book::all();

        foreach ($books as $book) {
            $reviewers = $users->random(rand(1,5));

            foreach ($reviewers as $reviewer) {
                Review::factory()->create([
                    'id_user' => $reviewer->id_user,
                    'id_book' => $book ->id_book,
                    'review' => fake()->paragraph(),
                    'rating' => fake()->numberBetween(3,5), 
                ]);
            }
        }
    }
}
