<?php

namespace App\Services;

use App\Enums\OrderStatus;
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
    public function checkout(User $user, array $validatedData): Order
    {
        return DB::transaction(function () use ($user, $validatedData) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            $cartItems = CartItem::query()
                ->where('cart_id', $cart->id)
                ->get();

            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Корзина пуста.']);
            }

            $productIds = $cartItems->pluck('product_id')->all();
            $products = Product::query()
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            foreach ($cartItems as $cartItem) {
                $product = $products->get($cartItem->product_id);
                if ($product === null || !$product->is_active) {
                    throw ValidationException::withMessages([
                        'product_id' => "Товар #{$cartItem->product_id} недоступен.",
                    ]);
                }
            }

            $order = Order::create([
                'user_id' => $user->id,
                'status' => OrderStatus::Pending,                                                                           // cast в модели сохранит value
                'customer_email' => $validatedData['customer_email'],
                'customer_phone' => $validatedData['customer_phone'],
                'customer_address' => $validatedData['customer_address'],
                'delivery_time' => $validatedData['delivery_time'],
                'total' => 0,
            ]);

            $totalInCents = 0;

            foreach ($cartItems as $cartItem) {
                $product = $products->get($cartItem->product_id);
                $unitPriceInCents = $this->rubToKop((string)$product->price);
                $subtotalInCents = $unitPriceInCents * (int)$cartItem->quantity;

                $totalInCents += $subtotalInCents;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => (int)$cartItem->quantity,
                    'price' => $product->price,
                ]);
            }

            $order->update(['total' => $this->kopToRub($totalInCents)]);

            CartItem::where('cart_id', $cart->id)->delete();

            return $order->load('items.product');
        });
    }

    private function rubToKop(string $amount): int
    {
        $normalized = str_replace([' ', "\u{00A0}", ','], ['', '', '.'], trim($amount));
        $isNegative = str_starts_with($normalized, '-');
        if ($isNegative) {
            $normalized = substr($normalized, 1);
        }

        [$rub, $kop] = array_pad(explode('.', $normalized, 2), 2, '0');
        $rub = preg_replace('/\D/', '', $rub);
        $kop = substr(preg_replace('/\D/', '', $kop) . '00', 0, 2);

        $value = (int)$rub * 100 + (int)$kop;

        return $isNegative ? -$value : $value;
    }

    private function kopToRub(int $kopecks): string
    {
        $isNegative = $kopecks < 0;
        $kopecks = abs($kopecks);

        return ($isNegative ? '-' : '')
            . intdiv($kopecks, 100)
            . '.'
            . str_pad((string)($kopecks % 100), 2, '0', STR_PAD_LEFT);
    }


}
