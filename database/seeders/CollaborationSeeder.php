<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CollaborationSeeder extends Seeder
{
    public function run(): void
    {
        // Create users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $user2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $user3 = User::create([
            'name' => 'Bob Builder',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Create lists
        $list1 = TaskList::create(['name' => 'Project Alpha', 'user_id' => $user1->id]);
        $list2 = TaskList::create(['name' => 'Weekend Chores', 'user_id' => $user1->id]);
        $list3 = TaskList::create(['name' => 'Team Building', 'user_id' => $user2->id]);

        // Add collaborators
        $list1->collaborators()->attach($user2->id, ['role' => 'member']);
        $list1->collaborators()->attach($user3->id, ['role' => 'member']);
        $list3->collaborators()->attach($user1->id, ['role' => 'owner']); // Give user1 owner role on user2's list just to test

        // Add tasks to list 1 (4 tasks, 2 completed -> 50% progress)
        Task::create(['list_id' => $list1->id, 'title' => 'Setup database', 'is_completed' => true]);
        Task::create(['list_id' => $list1->id, 'title' => 'Create models', 'is_completed' => true]);
        Task::create(['list_id' => $list1->id, 'title' => 'Design API', 'is_completed' => false]);
        Task::create(['list_id' => $list1->id, 'title' => 'Write tests', 'is_completed' => false]);

        // Add tasks to list 2 (2 tasks, 0 completed -> 0% progress)
        Task::create(['list_id' => $list2->id, 'title' => 'Buy groceries', 'is_completed' => false]);
        Task::create(['list_id' => $list2->id, 'title' => 'Clean room', 'is_completed' => false]);

        // Add tasks to list 3 (3 tasks, 3 completed -> 100% progress)
        Task::create(['list_id' => $list3->id, 'title' => 'Book venue', 'is_completed' => true]);
        Task::create(['list_id' => $list3->id, 'title' => 'Send invites', 'is_completed' => true]);
        Task::create(['list_id' => $list3->id, 'title' => 'Order food', 'is_completed' => true]);
    }
}
