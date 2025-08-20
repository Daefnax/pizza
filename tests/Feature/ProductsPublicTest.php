<?php

namespace Tests\Feature;

use App\Models\Product;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProductsPublicTest extends TestCase
{
    public function test_products_index_success(): void
    {
        Product::factory()->count(3)->create();

        $this->json('GET', '/api/products')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data']);
    }

    public function test_products_show_success(): void
    {
        $product = Product::factory()->create();

        $this->json('GET', "/api/products/{$product->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data']);
    }

    public function test_products_show_not_found(): void
    {
        $this->json('GET', '/api/products/999999')
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
