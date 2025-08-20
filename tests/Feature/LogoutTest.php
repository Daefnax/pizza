<?php

namespace Tests\Feature;

use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    public function test_logout_success(): void
    {
        $this->authJson('POST', '/api/logout')->assertStatus(Response::HTTP_OK);
    }

    public function test_logout_unauthorized(): void
    {
        $this->json('POST', '/api/logout')->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
