<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class ProductsPublicTest extends TestCase
{
    public function test_products_index_success(): void
    {
        Product::factory()->count(3)->create();

        $this->json('GET', '/api/products')
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_products_show_success(): void
    {
        $product = Product::factory()->create();

        $this->json('GET', "/api/products/{$product->id}")
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_products_show_not_found(): void
    {
        $this->json('GET', '/api/products/999999')
            ->assertStatus(404);
    }
}
