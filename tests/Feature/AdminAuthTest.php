<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => Hash::make('rahasia123'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_updates_last_login_at(): void
    {
        $admin = User::factory()->admin()->create(['password' => Hash::make('rahasia123')]);

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'rahasia123']);

        $this->assertNotNull($admin->fresh()->last_login_at);
    }

    public function test_superadmin_can_login(): void
    {
        $super = User::factory()->superAdmin()->create(['password' => Hash::make('rahasia123')]);

        $this->post('/admin/login', ['email' => $super->email, 'password' => 'rahasia123'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($super);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $admin = User::factory()->admin()->create(['password' => Hash::make('rahasia123')]);

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'salah'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_customer_cannot_login_to_admin_panel(): void
    {
        $user = User::factory()->create(['password' => Hash::make('rahasia123')]);

        $this->post('/admin/login', ['email' => $user->email, 'password' => 'rahasia123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_admin_cannot_login(): void
    {
        $admin = User::factory()->admin()->inactive()->create(['password' => Hash::make('rahasia123')]);

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'rahasia123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_five_attempts(): void
    {
        $admin = User::factory()->admin()->create(['password' => Hash::make('rahasia123')]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', ['email' => $admin->email, 'password' => 'salah']);
        }

        $response = $this->post('/admin/login', ['email' => $admin->email, 'password' => 'rahasia123']);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_logout(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/logout')->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
    }

    public function test_inactive_admin_is_kicked_out_of_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();

        $admin->update(['is_active' => false]);

        $this->actingAs($admin)->get('/admin/dashboard')->assertRedirect(route('login'));
        $this->assertGuest();
    }
}