<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class AdminProductsTest extends TestCase
{
    public function test_admin_products_index_success(): void
    {
        $admin = $this->adminUser();

        $this->authJson('GET', '/api/admin/products', [], $admin)
            ->assertStatus(200);
    }

    public function test_admin_products_index_forbidden_for_user(): void
    {
        $this->authJson('GET', '/api/admin/products')
            ->assertStatus(403);
    }

    public function test_admin_products_store_success(): void
    {
        $admin = $this->adminUser();

        $payload = [
            'name'      => 'New Product',
            'type'      => 'pizza',
            'price'     => '199.90',
            'is_active' => true,
        ];

        $this->authJson('POST', '/api/admin/products', $payload, $admin)
            ->assertStatus(201)
            ->assertJsonStructure(['data'=>['id','name','type','price']]);
    }

    public function test_admin_products_store_validation_error(): void
    {
        $admin = $this->adminUser();

        $this->authJson('POST', '/api/admin/products', [], $admin)
            ->assertStatus(422);
    }

    public function test_admin_products_show_and_update_destroy(): void
    {
        $admin   = $this->adminUser();
        $product = Product::factory()->create();

        $this->authJson('GET', "/api/admin/products/{$product->id}", [], $admin)
            ->assertStatus(200);

        $this->authJson('PUT', "/api/admin/products/{$product->id}", [
            'name'  => 'Updated',
            'type'  => 'drink',
            'price' => '49.99',
            'is_active' => true,
        ], $admin)->assertStatus(200);

        $this->authJson('PATCH', "/api/admin/products/{$product->id}/toggle", [], $admin)
            ->assertStatus(200);

        $this->authJson('DELETE', "/api/admin/products/{$product->id}", [], $admin)
            ->assertStatus(200);
    }

    public function test_admin_products_show_not_found(): void
    {
        $admin = $this->adminUser();
        $this->authJson('GET', '/api/admin/products/999999', [], $admin)->assertStatus(404);
    }
}
