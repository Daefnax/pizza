<?php

namespace Tests;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

trait CreatesJwt
{
    protected function authJson(string $method, string $uri, array $data = [], ?User $user = null)
    {
        $user  = $user ?? User::factory()->create();
        $token = JWTAuth::fromUser($user);

        return $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json',
        ])->json($method, $uri, $data);
    }
}
