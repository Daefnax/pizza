<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\Contracts\CartServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService implements CartServiceInterface
{
    public function get(User $user): array
    {
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $cart->load('items.product');

        $items = [];
        $totalKop = 0;

        foreach ($cart->items as $i) {
            $priceStr = (string)($i->product?->price ?? '0.00');
            $priceKop = $this->rubToKop($priceStr);
            $qty = (int)$i->quantity;
            $subtotalKop = $priceKop * $qty;

            $items[] = [
                'product_id' => $i->product_id,
                'name' => $i->product?->name,
                'quantity' => $qty,
                'unit_price' => $this->kopToRub($priceKop),
                'subtotal' => $this->kopToRub($subtotalKop),
            ];

            $totalKop += $subtotalKop;
        }

        return [
            'id' => $cart->id,
            'items' => $items,
            'total' => $this->kopToRub($totalKop),
        ];
    }

    public function add(User $user, int $product, int $quantity): array
    {
        if ($quantity < 1) {
            throw ValidationException::withMessages(['quantity' => 'Минимум 1.']);
        }

        return DB::transaction(function () use ($user, $product, $quantity) {
            $product = Product::query()
                ->whereKey($product)
                ->where('is_active', true)
                ->first();

            if (!$product) {
                throw ValidationException::withMessages(['product_id' => 'Товар недоступен.']);
            }

            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $item = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            $counts = $this->countItemsByType($cart);
            $type = $product->type;

            $currentQty = $item?->quantity ?? 0;
            $newQty = $currentQty + $quantity;

            if ($type === 'pizza' && ($counts['pizza'] - $currentQty + $newQty) > 10) {
                throw ValidationException::withMessages(['quantity' => 'Максимум 10 пицц в корзине.']);
            }
            if ($type === 'drink' && ($counts['drink'] - $currentQty + $newQty) > 20) {
                throw ValidationException::withMessages(['quantity' => 'Максимум 20 напитков в корзине.']);
            }

            if ($item) {
                $item->quantity = $newQty;
                $item->save();
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]);
            }

            return $this->get($user);
        });
    }

    public function update(User $user, int $product, int $quantity): array
    {
        if ($quantity < 0) {
            throw ValidationException::withMessages(['quantity' => 'Не может быть отрицательным.']);
        }

        return DB::transaction(function () use ($user, $product, $quantity) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $item = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('product_id', $product)
                ->lockForUpdate()
                ->first();

            if (!$item) {
                throw ValidationException::withMessages(['product_id' => 'Товар отсутствует в корзине.']);
            }

            if ($quantity === 0) {
                $item->delete();
            } else {
                $active = Product::query()
                    ->whereKey($product)
                    ->where('is_active', true)
                    ->first();

                if (!$active) {
                    throw ValidationException::withMessages(['product_id' => 'Товар недоступен.']);
                }

                $counts = $this->countItemsByType($cart);
                $type = $active->type;

                if ($type === 'pizza' && ($counts['pizza'] - $item->quantity + $quantity) > 10) {
                    throw ValidationException::withMessages(['quantity' => 'Максимум 10 пицц в корзине.']);
                }
                if ($type === 'drink' && ($counts['drink'] - $item->quantity + $quantity) > 20) {
                    throw ValidationException::withMessages(['quantity' => 'Максимум 20 напитков в корзине.']);
                }

                $item->quantity = $quantity;
                $item->save();
            }

            return $this->get($user);
        });
    }


    public function remove(User $user, int $product): array
    {
        return DB::transaction(function () use ($user, $product) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $qtyToRemove = (int)(app('request')->input('quantity', 0));
            $item = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('product_id', $product)
                ->lockForUpdate()
                ->first();

            if (!$item) {
                throw ValidationException::withMessages(['product_id' => 'Товар отсутствует в корзине.']);
            }

            if ($qtyToRemove < 1) {
                $item->delete();
            } else {
                $newQty = $item->quantity - $qtyToRemove;
                if ($newQty <= 0) {
                    $item->delete();
                } else {
                    $item->quantity = $newQty;
                    $item->save();
                }
            }

            return $this->get($user);
        });
    }

    public function clear(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            CartItem::where('cart_id', $cart->id)->delete();
            return $this->get($user);
        });
    }

    private function rubToKop(string $amount): int
    {
        $s = str_replace([' ', "\u{00A0}", ','], ['', '', '.'], trim($amount));
        $neg = str_starts_with($s, '-');
        if ($neg) {
            $s = substr($s, 1);
        }
        [$r, $k] = array_pad(explode('.', $s, 2), 2, '0');
        $r = preg_replace('/\D/', '', $r);
        $k = substr(preg_replace('/\D/', '', $k) . '00', 0, 2);
        $v = (int)$r * 100 + (int)$k;
        return $neg ? -$v : $v;
    }

    private function kopToRub(int $kop): string
    {
        $neg = $kop < 0;
        $kop = abs($kop);
        return ($neg ? '-' : '') . intdiv($kop, 100) . '.' . str_pad((string)($kop % 100), 2, '0', STR_PAD_LEFT);
    }

    public function countItemsByType(Cart $cart): array
    {
        $cart->load('items.product');

        $counts = [
            'pizza' => 0,
            'drink' => 0,
        ];

        foreach ($cart->items as $item) {
            $type = $item->product?->type;
            if ($type && isset($counts[$type])) {
                $counts[$type] += $item->quantity;
            }
        }

        return $counts;
    }
}
