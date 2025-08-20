<?php

namespace Tests\Feature;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
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
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['user','token']);
    }

    public function test_register_post_validation_error(): void
    {
        $this->json('POST', '/api/register', [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email','password']);
    }

    public function test_login_post_success(): void
    {
        $user = User::factory()->create(['email' => 'u@example.com', 'password' => bcrypt('password')]);

        $this->json('POST', '/api/login', [
            'email'    => 'u@example.com',
            'password' => 'password',
        ])->assertStatus(Response::HTTP_OK)->assertJsonStructure(['token']);
    }

    public function test_login_post_invalid_credentials(): void
    {
        $this->json('POST', '/api/login', [
            'email'    => 'nope@example.com',
            'password' => 'wrong',
        ])->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
