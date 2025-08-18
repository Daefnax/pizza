<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (empty(config('jwt.secret'))) {
            Config::set('jwt.secret', Str::random(64));
        }
        Config::set('jwt.ttl', 60);
    }

    protected function authJson(string $method, string $uri, array $data = [], ?User $user = null)
    {
        $user  = $user ?? User::factory()->create();

        $this->actingAs($user, 'api');

        $token = JWTAuth::fromUser($user);

        return $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json',
        ])->json($method, $uri, $data);
    }

    protected function adminUser(): User
    {
        return User::factory()->admin()->create();
    }

    protected function regularUser(): User
    {
        return User::factory()->create([
            'is_admin' => false,
        ]);
    }
}
