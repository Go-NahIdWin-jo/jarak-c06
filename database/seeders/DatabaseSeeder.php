<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun Admin
        User::create([
            'name' => 'Admin Jarak',
            'email' => 'admin@jarak.test',
            'password' => 'password123', // auto-hashed via cast 'password' => 'hashed'
            'role' => 'admin',
        ]);

        // Akun User Biasa
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@jarak.test',
            'password' => 'password123',
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Siti Rahma',
            'email' => 'siti@jarak.test',
            'password' => 'password123',
            'role' => 'user',
        ]);
    }
}
