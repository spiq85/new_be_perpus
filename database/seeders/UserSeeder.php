<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@perpus.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        $petugas = User::factory()->create([
            'username' => 'petugas',
            'email' => 'petugas@perpus.com',
            'password' => Hash::make('petugas123'),
            'role' => 'petugas',
        ]);
        $petugas->assignRole('petugas');

        $user = User::factory()->create([
            'username' => 'user',
            'email' => 'user@perpus.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
        ]);

        $user->assignRole('user');

        User::factory()->count(50)->create()->each(function ($user){
            $user->assignRole('user');
        });
    }
}
