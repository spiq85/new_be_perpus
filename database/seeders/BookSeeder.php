<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Category;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::pluck('id_category');

        Book::factory()->count(50)->create()->each(function ($book) use ($categories){ 
            $book->categories()->attach(
                $categories->random(rand(1,3))->toArray()
            );
        }); 
    }
}
