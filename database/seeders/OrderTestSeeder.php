<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrderTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            if (!app()->environment('local')) return;

        $user = User::updateOrCreate(
            ['email' => 'test@test.com'],
            [
                'name' => 'Test Order',
                'phone' => '123456789',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ]
        );

        $order = Order::updateOrCreate(
            ['user_id' => $user->id],
            [
                'customer_email' => 'test@test.com',
                'customer_phone' => '123456789',
                'customer_address' => 'test address',
                'delivery_time' => now()->addHours(2),
                'total' => 0,
            ]
        );


        $products = Product::take(3)->get();
        if ($products->isNotEmpty()) return;

        foreach ($products as $product) {
            orderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => 2,
                'price' => $product->price,
            ]);
        }

        $total = $order->items()
            ->selectRaw('COALESCE(SUM(quantity * price), 0) as total')
            ->value('total') ?? 0;
        $order->update(['total' => $total]);
    }
}
