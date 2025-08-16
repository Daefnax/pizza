<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_registers_a_user_with_valid_data(): void
    {
        $payload = [
            'name'                  => 'User',
            'phone'                 => '+1234567890',
            'email'                 => 'user@example.com',
            'password'              => 'password',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertCreated()
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'phone'],
                'token'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'user@example.com',
        ]);
    }
}
