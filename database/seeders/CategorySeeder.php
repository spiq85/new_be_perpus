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
            ['category_name' => 'Fiksi Ilmiah'],
            ['category_name' => 'Misteri'],
            ['category_name' => 'Fantasi'],
            ['category_name' => 'Sejarah'],
            ['category_name' => 'Biografi'],
            ['category_name' => 'Teknologi'],
        ];
        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
