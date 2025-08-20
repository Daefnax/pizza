<?php

namespace App\Models;

use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_items')->withPivot('quantity')->withTimestamps();
    }

    public function getItemsPayloadAttribute(): array
    {
        $items = $this->relationLoaded('items')
            ? $this->items
            : $this->items()->with('product')->get();

        return $items->map(function (CartItem $it): array {
            $product = $it->product;
            $qty = (int)$it->quantity;
            $unit = (string)($product?->price ?? '0.00');
            $subtotal = self::moneyMul($unit, (string)$qty);

            return [
                'product_id' => $product?->id,
                'name' => $product?->name,
                'type' => $product?->type?->value,
                'quantity' => $qty,
                'unit_price' => self::moneyFmt($unit),
                'subtotal' => self::moneyFmt($subtotal),
            ];
        })->all();
    }

    public function getTotalAttribute(): string
    {
        $items = $this->relationLoaded('items')
            ? $this->items
            : $this->items()->with('product')->get();

        $sum = '0.00';
        foreach ($items as $it) {
            $price = (string)($it->product?->price ?? '0.00');
            $subtotal = self::moneyMul($price, (string)(int)$it->quantity);
            $sum = self::moneyAdd($sum, $subtotal);
        }
        return self::moneyFmt($sum);
    }

    public function getPizzaInCartAttribute(): int
    {
        $items = $this->relationLoaded('items')
            ? $this->items
            : $this->items()->with('product')->get();

        return $items->reduce(function (int $carry, CartItem $it): int {
            return $carry + (($it->product?->type === ProductType::Pizza) ? (int)$it->quantity : 0);
        }, 0);
    }

    public function getDrinkInCartAttribute(): int
    {
        $items = $this->relationLoaded('items')
            ? $this->items
            : $this->items()->with('product')->get();

        return $items->reduce(function (int $carry, CartItem $it): int {
            return $carry + (($it->product?->type === ProductType::Drink) ? (int)$it->quantity : 0);
        }, 0);
    }

    public function getLimitsAttribute(): array
    {
        return [
            'pizza_max' => (int)config('cart.limits.' . ProductType::Pizza->value, 10),
            'drink_max' => (int)config('cart.limits.' . ProductType::Drink->value, 20),
            'pizza_in_cart' => $this->pizza_in_cart,
            'drink_in_cart' => $this->drink_in_cart,
        ];
    }

    private static function moneyMul(string $a, string $b): string
    {
        if (function_exists('bcmul')) {
            return bcmul($a, $b, 2);
        }
        return number_format(((float)$a) * (float)$b, 2, '.', '');
    }

    private static function moneyAdd(string $a, string $b): string
    {
        if (function_exists('bcadd')) {
            return bcadd($a, $b, 2);
        }
        return number_format(((float)$a) + (float)$b, 2, '.', '');
    }

    private static function moneyFmt(string $v): string
    {
        return number_format((float)$v, 2, '.', '');
    }
}

