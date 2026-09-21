<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_admin_users(): void
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect('/login');
    }

    public function test_regular_user_receives_403_when_accessing_admin_routes(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/users');

        $response->assertForbidden();
    }

    public function test_admin_can_view_users_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $otherUser = User::factory()->create(['name' => 'Jane Doe', 'role' => 'user']);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertOk();
        $response->assertSee('Manage Users');
        $response->assertSee('Jane Doe');
    }

    public function test_admin_can_view_create_user_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/users/create');

        $response->assertOk();
        $response->assertSee('Tambah User Baru');
    }

    public function test_admin_can_create_new_user_atomically(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $userData = [
            'name' => 'Alice Wonder',
            'email' => 'alice@example.com',
            'password' => 'secret1234',
            'role' => 'user',
        ];

        $response = $this->actingAs($admin)->post('/admin/users', $userData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'User berhasil ditambahkan.');

        $this->assertDatabaseHas('users', [
            'name' => 'Alice Wonder',
            'email' => 'alice@example.com',
            'role' => 'user',
        ]);
    }

    public function test_admin_cannot_create_user_with_duplicate_email(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Another User',
            'email' => 'duplicate@example.com',
            'password' => 'secret1234',
            'role' => 'user',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_admin_can_delete_other_user_atomically(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $targetUser = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($admin)->delete("/admin/users/{$targetUser->id}");

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'User berhasil dihapus.');

        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
        ]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");

        $response->assertSessionHas('error', 'Tidak bisa menghapus akun sendiri.');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }
}
