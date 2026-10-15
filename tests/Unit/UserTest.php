<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_helpers(): void
    {
        $admin = User::factory()->admin()->make();
        $super = User::factory()->superAdmin()->make();
        $user = User::factory()->make();

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isSuperAdmin());

        $this->assertTrue($super->isAdmin());
        $this->assertTrue($super->isSuperAdmin());

        $this->assertFalse($user->isAdmin());
    }

    public function test_role_labels(): void
    {
        $this->assertSame('Admin', User::factory()->admin()->make()->role_label);
        $this->assertSame('Super Admin', User::factory()->superAdmin()->make()->role_label);
        $this->assertSame('Pelanggan', User::factory()->make()->role_label);
    }

    public function test_initials(): void
    {
        $this->assertSame('BS', (new User(['name' => 'Budi Santoso']))->initials);
        $this->assertSame('A', (new User(['name' => 'Ayu']))->initials);
        $this->assertSame('?', (new User(['name' => '   ']))->initials);
    }

    public function test_password_is_hashed_automatically(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123']);

        $this->assertNotSame('rahasia123', $user->password);
        $this->assertTrue(password_verify('rahasia123', $user->password));
    }

    public function test_password_is_hidden_from_serialization(): void
    {
        $user = User::factory()->create();

        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    public function test_admins_scope_only_returns_active_admins(): void
    {
        User::factory()->admin()->create();
        User::factory()->superAdmin()->create();
        User::factory()->create(); // pelanggan
        User::factory()->admin()->inactive()->create();

        $this->assertSame(2, User::admins()->count());
    }

    public function test_super_admins_scope(): void
    {
        User::factory()->superAdmin()->create();
        User::factory()->admin()->create();

        $this->assertSame(1, User::superAdmins()->count());
    }

    public function test_active_scope(): void
    {
        User::factory()->count(2)->create();
        User::factory()->inactive()->create();

        $this->assertSame(2, User::active()->count());
    }

    public function test_casts(): void
    {
        $user = User::factory()->create(['is_active' => 1]);

        $this->assertIsBool($user->is_active);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->last_login_at ?? now());
    }
}