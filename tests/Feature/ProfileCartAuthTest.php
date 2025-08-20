<?php

namespace Tests\Feature;

use App\Models\Product;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProfileCartAuthTest extends TestCase
{
    public function test_me_success(): void
    {
        $this->authJson('GET', '/api/me')->assertStatus(Response::HTTP_OK)->assertJsonStructure(['id','email']);
    }

    public function test_me_unauthorized(): void
    {
        $this->json('GET', '/api/me')->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_cart_show_success(): void
    {
        $response = $this->authJson('GET', '/api/cart');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'products',
                'limits' => ['pizza_max', 'drink_max', 'pizza_in_cart', 'drink_in_cart'],
                'total',
                'created_at',
                'updated_at',
            ]
        ]);
    }

    public function test_cart_show_unauthorized(): void
    {
        $this->json('GET', '/api/cart')->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_cart_add_success(): void
    {
        $product = Product::factory()->create();

        $this->authJson('POST', '/api/cart/items', [
            'product_id' => $product->id,
            'quantity'   => 2,
        ])->assertStatus(Response::HTTP_OK)->assertJsonStructure([
            'data' => [
                'id',
                'products',
                'total',
            ],
        ]);
    }

    public function test_cart_add_invalid_product(): void
    {
        $this->authJson('POST', '/api/cart/items', [
            'product_id' => 999999,
            'quantity'   => 1,
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonValidationErrors(['product_id']);
    }

    public function test_cart_update_success(): void
    {
        $user    = $this->regularUser();
        $product = Product::factory()->create();

        $this->authJson('POST', '/api/cart/items', ['product_id'=>$product->id,'quantity'=>1], $user)->assertStatus(200);

        $this->authJson('PUT', "/api/cart/items/{$product->id}", ['quantity'=>3], $user)
            ->assertStatus(Response::HTTP_OK);
    }

    public function test_cart_update_invalid_quantity(): void
    {
        $product = Product::factory()->create();
        $this->authJson('POST', '/api/cart/items', ['product_id'=>$product->id,'quantity'=>1])->assertStatus(200);

        $this->authJson('PUT', "/api/cart/items/{$product->id}", ['quantity'=>-1])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonValidationErrors(['quantity']);
    }

    public function test_cart_remove_success(): void
    {
        $user    = $this->regularUser();
        $product = Product::factory()->create();

        $this->authJson('POST', '/api/cart/items', [
            'product_id' => $product->id,
            'quantity'   => 2,
        ], $user)->assertStatus(Response::HTTP_OK);

        $this->authJson('DELETE', "/api/cart/items/{$product->id}", [], $user)
            ->assertStatus(Response::HTTP_OK);
    }

    public function test_cart_remove_not_in_cart(): void
    {
        $this->authJson('DELETE', '/api/cart/items/999999')->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_cart_clear_success(): void
    {
        $this->authJson('DELETE', '/api/cart')->assertStatus(Response::HTTP_OK);
    }
}
