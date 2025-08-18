<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthRoutesTest extends TestCase
{
    public function test_register_post_success(): void
    {
        $payload = [
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'phone'    => '+79990000000',
            'password' => 'password',
        ];

        $this->json('POST', '/api/register', $payload)
            ->assertStatus(201)
            ->assertJsonStructure(['user','token']);
    }

    public function test_register_post_validation_error(): void
    {
        $this->json('POST', '/api/register', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email','password']);
    }

    public function test_login_post_success(): void
    {
        $user = User::factory()->create(['email' => 'u@example.com', 'password' => bcrypt('password')]);

        $this->json('POST', '/api/login', [
            'email'    => 'u@example.com',
            'password' => 'password',
        ])->assertStatus(200)->assertJsonStructure(['token']);
    }

    public function test_login_post_invalid_credentials(): void
    {
        $this->json('POST', '/api/login', [
            'email'    => 'nope@example.com',
            'password' => 'wrong',
        ])->assertStatus(401);
    }
}
