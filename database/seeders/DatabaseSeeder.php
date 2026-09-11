<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
<<<<<<< HEAD
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $owner = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        $collaborator = User::factory()->create([
            'name' => 'Collaborator User',
            'email' => 'collaborator@example.com',
            'role' => 'user',
        ]);

        $list = TaskList::create([
            'name' => 'Tugas Kuliah',
            'user_id' => $owner->id,
        ]);

        $list->members()->attach($collaborator->id);

        Task::create([
            'list_id' => $list->id,
            'title' => 'Kerjakan laporan PPK',
            'deadline' => now()->addDays(5),
            'priority' => 'high',
            'is_completed' => false,
        ]);

        Task::create([
            'list_id' => $list->id,
            'title' => 'Review kode teman',
            'deadline' => now()->addDays(2),
            'priority' => 'medium',
            'is_completed' => true,
=======
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
>>>>>>> feature/auth-user-management
        ]);
    }
}