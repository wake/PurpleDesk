<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_register(): void
    {
        $organization = Organization::factory()->create();
        
        $userData = [
            'account' => 'test_user_' . rand(1000, 9999),
            'full_name' => $this->faker->name,
            'display_name' => $this->faker->firstName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => ['id', 'account', 'full_name', 'display_name', 'email'],
                'token'
            ]);

        $this->assertDatabaseHas('users', [
            'account' => $userData['account'],
            'full_name' => $userData['full_name'],
            'email' => $userData['email'],
        ]);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'account' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'login' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'account', 'full_name', 'display_name', 'email'],
                'token'
            ]);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => '登出成功']);
    }

    public function test_invalid_login_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'login' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJson(['message' => '登入資訊無效']);
    }
}
