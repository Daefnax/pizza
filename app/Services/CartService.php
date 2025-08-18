<?php

namespace App\Services;

use App\Enums\ProductType;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\Contracts\CartServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService implements CartServiceInterface
{
    public function get(User $user): Cart
    {
        return Cart::with('items.product')->firstOrCreate(['user_id' => $user->id]);
    }

    public function add(User $user, int $productId, int $quantity): Cart
    {
        if ($quantity < 1) {
            throw ValidationException::withMessages(['quantity' => 'Минимум 1.']);
        }

        return DB::transaction(function () use ($user, $productId, $quantity) {
            $product = $this->getActiveProduct($productId);
            $type = $product->type;

            $cart = $this->getOrCreateCart($user);

            $existingItem = $this->findCartItem($cart, $product->id);
            $currentQuantity = $existingItem?->quantity ?? 0;
            $newQuantity = $currentQuantity + $quantity;

            $this->validateQuantityLimit(
                cart: $cart,
                type: $type,
                newQuantity: $newQuantity,
                previousQuantity: $currentQuantity
            );

            if ($existingItem) {
                $existingItem->update(['quantity' => $newQuantity]);
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

    public function update(User $user, int $productId, int $quantity): Cart
    {
        if ($quantity < 0) {
            throw ValidationException::withMessages(['quantity' => 'Не может быть отрицательным.']);
        }

        return DB::transaction(function () use ($user, $productId, $quantity) {
            $cart = $this->getOrCreateCart($user);

            $existingItem = $this->findCartItem($cart, $productId);

            if (!$existingItem) {
                throw ValidationException::withMessages(['product_id' => 'Товар отсутствует в корзине.']);
            }

            if ($quantity === 0) {
                $existingItem->delete();
            } else {
                $product = $this->getActiveProduct($productId);
                $type = $product->type;

                $this->validateQuantityLimit(
                    cart: $cart,
                    type: $type,
                    newQuantity: $quantity,
                    previousQuantity: $existingItem->quantity
                );

                $existingItem->update(['quantity' => $quantity]);
            }

            return $this->get($user);
        });
    }

    public function remove(User $user, int $productId, ?int $quantity = null): Cart
    {
        return DB::transaction(function () use ($user, $productId, $quantity) {
            $cart = $this->getOrCreateCart($user);

            $existingItem = $this->findCartItem($cart, $productId);

            if (!$existingItem) {
                throw ValidationException::withMessages(['product_id' => 'Товар отсутствует в корзине.']);
            }

            if ($quantity === null || $quantity >= $existingItem->quantity) {
                $existingItem->delete();
            } else {
                $newQuantity = $existingItem->quantity - $quantity;
                $existingItem->update(['quantity' => $newQuantity]);
            }

            return $this->get($user);
        });
    }

    public function clear(User $user): Cart
    {
        return DB::transaction(function () use ($user) {
            $cart = $this->getOrCreateCart($user);
            CartItem::where('cart_id', $cart->id)->delete();
            return $this->get($user);
        });
    }

    private function getActiveProduct(int $productId): Product
    {
        $product = Product::query()
            ->whereKey($productId)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            throw ValidationException::withMessages(['product_id' => 'Товар недоступен.']);
        }

        return $product;
    }

    private function getOrCreateCart(User $user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    private function findCartItem(Cart $cart, int $productId): ?CartItem
    {
        return CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->first();
    }

    private function validateQuantityLimit(Cart $cart, ProductType $type, int $newQuantity, int $previousQuantity = 0): void
    {
        $limits = config('cart.limits');

        if (!isset($limits[$type->value])) {
            return;
        }

        $cart->loadMissing('items.product');

        $currentCount = $cart->items->reduce(
            fn(int $carry, CartItem $item) =>
                $carry + ($item->product?->type === $type ? $item->quantity : 0),
            0
        );

        $effectiveTotal = $currentCount - $previousQuantity + $newQuantity;

        if ($effectiveTotal > $limits[$type->value]) {
            throw ValidationException::withMessages([
                'quantity' => "Максимум {$limits[$type->value]} {$type->value} в корзине.",
            ]);
        }
    }
}
