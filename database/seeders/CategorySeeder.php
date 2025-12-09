<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['category_name' => 'Fiksi'],
            ['category_name' => 'Fiksi Ilmiah'],
            ['category_name' => 'Misteri'],
            ['category_name' => 'Fantasi'],
            ['category_name' => 'Sejarah'],
            ['category_name' => 'Biografi'],
            ['category_name' => 'Teknologi'],
            ['category_name' => 'Novel'],
            ['category_name' => 'Inspiratif'],
            ['category_name' => 'Indonesia'],
            ['category_name' => 'Petualangan'],
            ['category_name' => 'Remaja'],
            ['category_name' => 'Thriller'],
            ['category_name' => 'Psikologi'],
            ['category_name' => 'Non-Fiksi'],
            ['category_name' => 'Pengembangan Diri'],
            ['category_name' => 'Sains'],
            ['category_name' => 'Romansa'],
            ['category_name' => 'Religi'],
            ['category_name' => 'Keuangan'],
            ['category_name' => 'Bisnis'],
            ['category_name' => 'Komik'],
            ['category_name' => 'Anak'],
            ['category_name' => 'Klasik'],
            ['category_name' => 'Dystopia'],
            ['category_name' => 'Drama'],
            ['category_name' => 'Filosfi'],
            ['category_name' => 'Jepang'],
        ];
        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
