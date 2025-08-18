<?php

namespace Tests\Feature;

use Tests\TestCase;

class LogoutTest extends TestCase
{
    public function test_logout_success(): void
    {
        $this->authJson('POST', '/api/logout')->assertStatus(200);
    }

    public function test_logout_unauthorized(): void
    {
        $this->json('POST', '/api/logout')->assertStatus(401);
    }
}
