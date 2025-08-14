<?php

namespace App\Application\Cart;

use App\Domain\Cart\CartServiceInterface;
use App\Domain\Cart\Exceptions\CartLimitException;
use App\Domain\Cart\Exceptions\UnknownProductTypeException;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CartService implements CartServiceInterface
{
    private const LIMITS = [
        'pizza' => 10,
        'drink' => 20,
    ];

    public function getOrCreateForUser(int $id): Cart
    {
        return Cart::firstOrCreate(['user_id' => $id]);
    }

    public function addItem(int $id, Product $product, int $quantity): Cart
    {
        if ($quantity <= 0) {
            return $this->removeItem($id, $product);
        }

        return DB::transaction(function () use ($id, $product, $qty) {
            $cart = $this->getOrCreateWithLock($id);

            $item = $cart->items()
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            $newQty = $qty + ($item?->quantity ?? 0);

            $this->assertTypeLimitSql($cart->id, $product->type, $newQty, $product->id, false);

            if ($item) {
                $item->update(['quantity' => $newQty]);
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                ]);
            }

            return $cart->load('items.product');
        });
    }

    public function updateItem(int $id, Product $product, int $qty): Cart
    {
        return DB::transaction(function () use ($id, $product, $qty) {
            $cart = $this->getOrCreateWithLock($id);

            $item = $cart->items()
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($qty <= 0) {
                $item->delete();
                return $cart->load('items.product');
            }

            $this->assertTypeLimitSql($cart->id, $product->type, $qty, $product->id, true);

            $item->update(['quantity' => $qty]);

            return $cart->load('items.product');
        });
    }

    public function removeItem(int $id, Product $product): Cart
    {
        $cart = $this->getOrCreateWithLock($id);
        $cart->items()->where('product_id', $product->id)->delete();

        return $cart->load('items.product');
    }

    public function clear(int $id): Cart
    {
        $cart = $this->getOrCreateWithLock($id);
        $cart->items()->delete();

        return $cart->load('items.product');
    }

    private function getOrCreateWithLock(int $id): Cart
    {
        return Cart::where('user_id', $id)
            ->lockForUpdate()
            ->first() ?? Cart::create(['user_id' => $id]);
    }

    private function assertTypeLimitSql(
        int $cartId,
        string $type,
        int $candidateQty,
        int $productId,
        bool $isReplace
    ): void {
        $limit = self::LIMITS[$type] ?? null;
        if ($limit === null) {
            throw new UnknownProductTypeException("Неизвестный тип продукта: {$type}");
        }

        $query = DB::table('cart_items as ci')
            ->join('products as p', 'p.id', '=', 'ci.product_id')
            ->where('ci.cart_id', $cartId)
            ->where('p.type', $type);

        if ($isReplace) {
            $query->where('ci.product_id', '<>', $productId);
        }

        $currentSum = (int) $query->lockForUpdate()->sum('ci.quantity');

        $newTotal = $currentSum + $candidateQty;

        if ($newTotal > $limit) {
            throw new CartLimitException("Лимит по типу {$type} превышен: {$newTotal} > {$limit}");
        }
    }

    public function getCartForUser($id)
    {
    }
}
