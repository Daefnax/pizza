<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CartTestSeeder extends Seeder
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
                'password' => Hash::make('password'),
                'is_admin' => false
            ]
        );

        $cart = Cart::firstOrCreate(
            ['user_id', $user->id]
        );

        $cart->items()->delete();

        $products = Product::take(3)->get();
        if ($products->isEmpty()) return;

        foreach ($products as $product) {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => 2,
            ]);
        }

        $cart = Cart::with('items.product')->firstWhere('user_id', $user->id);
        $cart->total;
    }
}
