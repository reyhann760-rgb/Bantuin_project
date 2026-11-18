<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_login_page(): void
    {
        $response = $this->get(route('login'));
        $response->assertOk();
        $response->assertSee('Masuk');
    }

    public function test_user_can_view_register_page(): void
    {
        $response = $this->get(route('register'));
        $response->assertOk();
        $response->assertSee('Daftar');
    }

    public function test_customer_can_register_with_valid_data(): void
    {
        $response = $this->post(route('register.store'), [
            'name'                  => 'Budi Pelanggan',
            'email'                 => 'budi@example.com',
            'phone'                 => '081234567890',
            'role'                  => 'customer',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'role'  => 'customer',
        ]);
    }

    public function test_provider_can_register_with_valid_data(): void
    {
        $response = $this->post(route('register.store'), [
            'name'                  => 'Siti Penyedia',
            'email'                 => 'siti@example.com',
            'phone'                 => '081234567891',
            'role'                  => 'provider',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'siti@example.com',
            'role'  => 'provider',
        ]);
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post(route('register.store'), [
            'name'                  => 'Duplikat User',
            'email'                 => 'existing@example.com',
            'role'                  => 'customer',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email'    => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login.store'), [
            'email'    => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_incorrect_password(): void
    {
        $user = User::factory()->create([
            'email'    => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login.store'), [
            'email'    => 'user@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}

