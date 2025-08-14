<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function checkout(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            $cartItems = CartItem::query()
                ->where('cart_id', $cart->id)
                ->get();

            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Корзина пуста.',
                ]);
            }

            $productIds = $cartItems->pluck('product_id')->all();

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            $total = '0.00';

            foreach ($cartItems as $item) {
                $product = $products->get($item->product_id);

                if ($product === null || !$product->is_active) {
                    throw ValidationException::withMessages([
                        'product_id' => "Товар #{$item->product_id} недоступен.",
                    ]);
                }
            }

            $order = Order::create([
                'user_id' => $user->id,
                'status' => Order::STATUS_NEW,
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'customer_address' => $data['customer_address'],
                'delivery_time' => $data['delivery_time'],
                'total' => 0,
            ]);

            foreach ($cartItems as $item) {
                $product = $products->get($item->product_id);
                $subtotal = round((float)$product->price * $item->quantity, 2);
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'price' => $product->price,
                ]);
            }

            $order->update(['total' => $total]);

            CartItem::where('cart_id', $cart->id)->delete();

            return $order->load('items.product');
        });
    }
}
