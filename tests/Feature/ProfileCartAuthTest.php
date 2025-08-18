<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class ProfileCartAuthTest extends TestCase
{
    public function test_me_success(): void
    {
        $this->authJson('GET', '/api/me')->assertStatus(200)->assertJsonStructure(['id','email']);
    }

    public function test_me_unauthorized(): void
    {
        $this->json('GET', '/api/me')->assertStatus(401);
    }

    public function test_cart_show_success(): void
    {
        $response = $this->authJson('GET', '/api/cart');

        $response->assertStatus(200);
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
        $this->json('GET', '/api/cart')->assertStatus(401);
    }

    public function test_cart_add_success(): void
    {
        $product = Product::factory()->create();

        $this->authJson('POST', '/api/cart/items', [
            'product_id' => $product->id,
            'quantity'   => 2,
        ])->assertStatus(200)->assertJsonStructure([
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
        ])->assertStatus(422)->assertJsonValidationErrors(['product_id']);
    }

    public function test_cart_update_success(): void
    {
        $user    = $this->regularUser();
        $product = Product::factory()->create();

        $this->authJson('POST', '/api/cart/items', ['product_id'=>$product->id,'quantity'=>1], $user)->assertStatus(200);

        $this->authJson('PUT', "/api/cart/items/{$product->id}", ['quantity'=>3], $user)
            ->assertStatus(200);
    }

    public function test_cart_update_invalid_quantity(): void
    {
        $product = Product::factory()->create();
        $this->authJson('POST', '/api/cart/items', ['product_id'=>$product->id,'quantity'=>1])->assertStatus(200);

        $this->authJson('PUT', "/api/cart/items/{$product->id}", ['quantity'=>-1])
            ->assertStatus(422)->assertJsonValidationErrors(['quantity']);
    }

    public function test_cart_remove_success(): void
    {
        $user    = $this->regularUser();
        $product = Product::factory()->create();

        $this->authJson('POST', '/api/cart/items', [
            'product_id' => $product->id,
            'quantity'   => 2,
        ], $user)->assertStatus(200);

        $this->authJson('DELETE', "/api/cart/items/{$product->id}", [], $user)
            ->assertStatus(200);
    }

    public function test_cart_remove_not_in_cart(): void
    {
        $this->authJson('DELETE', '/api/cart/items/999999')->assertStatus(422);
    }

    public function test_cart_clear_success(): void
    {
        $this->authJson('DELETE', '/api/cart')->assertStatus(200);
    }
}
