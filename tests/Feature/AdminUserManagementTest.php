<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->superAdmin()->create();
    }

    public function test_superadmin_can_view_user_list(): void
    {
        User::factory()->count(3)->create();

        $this->actingAs($this->superAdmin())
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertViewHas('summary');
    }

    public function test_regular_admin_cannot_access_user_management(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_superadmin_can_create_user(): void
    {
        $this->actingAs($this->superAdmin())
            ->post(route('admin.users.store'), [
                'name' => 'Admin Baru',
                'email' => 'baru@dcemilinyuk.id',
                'role' => User::ROLE_ADMIN,
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['email' => 'baru@dcemilinyuk.id', 'role' => User::ROLE_ADMIN]);
    }

    public function test_create_user_validates_input(): void
    {
        $this->actingAs($this->superAdmin())
            ->post(route('admin.users.store'), [])
            ->assertSessionHasErrors(['name', 'email', 'role', 'password']);
    }

    public function test_create_user_rejects_duplicate_email(): void
    {
        $existing = User::factory()->create();

        $this->actingAs($this->superAdmin())
            ->post(route('admin.users.store'), [
                'name' => 'Duplikat',
                'email' => $existing->email,
                'role' => User::ROLE_ADMIN,
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_superadmin_can_update_user(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($this->superAdmin())
            ->put(route('admin.users.update', $user->id), [
                'name' => 'Nama Diubah',
                'email' => $user->email,
                'role' => User::ROLE_ADMIN,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertSame('Nama Diubah', $user->name);
        $this->assertSame(User::ROLE_ADMIN, $user->role);
    }

    public function test_updating_user_without_password_keeps_existing_password(): void
    {
        $user = User::factory()->create();
        $originalHash = $user->password;

        $this->actingAs($this->superAdmin())
            ->put(route('admin.users.update', $user->id), [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_active' => '1',
            ]);

        $this->assertSame($originalHash, $user->fresh()->password);
    }

    public function test_cannot_demote_last_superadmin(): void
    {
        $super = $this->superAdmin();

        $this->actingAs($super)
            ->put(route('admin.users.update', $super->id), [
                'name' => $super->name,
                'email' => $super->email,
                'role' => User::ROLE_ADMIN,
                'is_active' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertTrue($super->fresh()->isSuperAdmin());
    }

    public function test_cannot_deactivate_last_superadmin(): void
    {
        $super = $this->superAdmin();

        $this->actingAs($super)
            ->put(route('admin.users.update', $super->id), [
                'name' => $super->name,
                'email' => $super->email,
                'role' => User::ROLE_SUPERADMIN,
            ])
            ->assertSessionHas('error');

        $this->assertTrue($super->fresh()->is_active);
    }

    public function test_superadmin_can_delete_other_user(): void
    {
        $super = $this->superAdmin();
        $user = User::factory()->create();

        $this->actingAs($super)
            ->delete(route('admin.users.destroy', $user->id))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_cannot_delete_own_account(): void
    {
        $super = $this->superAdmin();

        $this->actingAs($super)
            ->delete(route('admin.users.destroy', $super->id))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $super->id]);
    }

    public function test_cannot_delete_last_superadmin(): void
    {
        $super = $this->superAdmin();
        $other = User::factory()->superAdmin()->create();

        $this->actingAs($super)
            ->delete(route('admin.users.destroy', $other->id));

        $this->assertDatabaseMissing('users', ['id' => $other->id]);
    }
}