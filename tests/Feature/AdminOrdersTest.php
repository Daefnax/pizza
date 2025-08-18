<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Product;
use Tests\TestCase;

class AdminOrdersTest extends TestCase
{
    public function test_admin_orders_index_success(): void
    {
        $admin = $this->adminUser();
        $this->authJson('GET', '/api/admin/orders', [], $admin)->assertStatus(200);
    }

    public function test_admin_orders_index_forbidden_for_user(): void
    {
        $this->authJson('GET', '/api/admin/orders')->assertStatus(403);
    }

    public function test_admin_update_status_success(): void
    {
        $user    = $this->regularUser();
        $product = Product::factory()->create();

        $this->authJson('POST', '/api/cart/items', [
            'product_id' => $product->id,
            'quantity'   => 1,
        ], $user)->assertStatus(200);

        $order = $this->authJson('POST', '/api/orders', [
            'customer_email'   => 'buyer@example.com',
            'customer_phone'   => '+79999999999',
            'customer_address' => 'адрес',
            'delivery_time'    => now()->addHours(2)->toIso8601String(),
        ], $user)->decodeResponseJson()['data'];

        $admin = $this->adminUser();

        $response = $this->authJson('PATCH', "/api/admin/orders/{$order['id']}/status", [
            'status' => OrderStatus::Processing->value,
        ], $admin);

        $response->assertStatus(200);
        $response->assertJsonPath('data.status', OrderStatus::Processing->value);
    }

    public function test_admin_update_status_invalid_value(): void
    {
        $admin = $this->adminUser();

        $this->authJson('PATCH', '/api/admin/orders/999999/status', [
            'status' => 'unknown',
        ], $admin)->assertStatus(422);
    }
}
