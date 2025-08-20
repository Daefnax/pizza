<?php

namespace Tests\Feature;

use App\Models\Product;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AdminProductsTest extends TestCase
{
    public function test_admin_products_index_success(): void
    {
        $admin = $this->adminUser();

        $this->authJson('GET', '/api/admin/products', [], $admin)
            ->assertStatus(Response::HTTP_OK);
    }

    public function test_admin_products_index_forbidden_for_user(): void
    {
        $this->authJson('GET', '/api/admin/products')
            ->assertStatus(Response::HTTP_FORBIDDEN);
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
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['data'=>['id','name','type','price']]);
    }

    public function test_admin_products_store_validation_error(): void
    {
        $admin = $this->adminUser();

        $this->authJson('POST', '/api/admin/products', [], $admin)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_admin_products_show_and_update_destroy(): void
    {
        $admin   = $this->adminUser();
        $product = Product::factory()->create();

        $this->authJson('GET', "/api/admin/products/{$product->id}", [], $admin)
            ->assertStatus(Response::HTTP_OK);

        $this->authJson('PUT', "/api/admin/products/{$product->id}", [
            'name'  => 'Updated',
            'type'  => 'drink',
            'price' => '49.99',
            'is_active' => true,
        ], $admin)->assertStatus(Response::HTTP_OK);

        $this->authJson('PATCH', "/api/admin/products/{$product->id}/toggle", [], $admin)
            ->assertStatus(Response::HTTP_OK);

        $this->authJson('DELETE', "/api/admin/products/{$product->id}", [], $admin)
            ->assertStatus(Response::HTTP_OK);
    }

    public function test_admin_products_show_not_found(): void
    {
        $admin = $this->adminUser();
        $this->authJson('GET', '/api/admin/products/999999', [], $admin)->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
