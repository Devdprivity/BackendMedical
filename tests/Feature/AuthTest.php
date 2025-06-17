<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_register()
    {
        $userData = [
            'name' => 'Dr. John Doe',
            'email' => 'john.doe@medical.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'doctor',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'status',
                    ],
                    'token',
                    'token_type'
                ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@medical.com',
            'role' => 'doctor',
            'status' => 'active',
        ]);
    }

    public function test_user_cannot_register_with_invalid_data()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'role' => 'invalid-role',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
    }

    public function test_user_cannot_register_with_existing_email()
    {
        User::factory()->create(['email' => 'existing@medical.com']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'existing@medical.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'doctor',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@medical.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@medical.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'status',
                    ],
                    'token',
                    'token_type'
                ]);

        // Check that last_login was updated
        $user->refresh();
        $this->assertNotNull($user->last_login);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'test@medical.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@medical.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    public function test_inactive_user_cannot_login()
    {
        User::factory()->create([
            'email' => 'inactive@medical.com',
            'password' => Hash::make('password123'),
            'status' => 'inactive',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'inactive@medical.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->postJson('/api/auth/logout');

        $response->assertStatus(200)
                ->assertJson(['message' => 'Logged out successfully']);

        // Token should be deleted
        $this->assertEquals(0, $user->tokens()->count());
    }

    public function test_authenticated_user_can_get_profile()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/auth/user');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'id',
                    'name',
                    'email',
                    'role',
                    'status',
                ]);
    }

    public function test_unauthenticated_user_cannot_access_protected_routes()
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(401);
    }

    public function test_user_with_invalid_token_cannot_access_protected_routes()
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
                        ->getJson('/api/auth/user');

        $response->assertStatus(401);
    }

    public function test_login_requires_email_and_password()
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_register_requires_password_confirmation()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@medical.com',
            'password' => 'password123',
            'role' => 'doctor',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }
}
