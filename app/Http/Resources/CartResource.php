<?php

namespace App\Http\Resources;

use App\Enums\ProductType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $pizzaCount = 0;
        $drinkCount = 0;
        $total = '0.00';

        $products = [];

        foreach ($this->items as $cartItem) {
            $product = $cartItem->product;
            $quantity = (int)$cartItem->quantity;

            $unitPrice = (string)($product?->price ?? '0.00');
            $subtotal = bcmul($unitPrice, (string)$quantity, 2);
            $total = bcadd($total, $subtotal, 2);

            $typeEnum = $product?->type;

            if ($typeEnum === ProductType::Pizza) {
                $pizzaCount += $quantity;
            } elseif ($typeEnum === ProductType::Drink) {
                $drinkCount += $quantity;
            }

            $products[] = [
                'id' => $product?->id,
                'name' => $product?->name,
                'type' => $typeEnum?->value,
                'price' => number_format($unitPrice, 2, '.', ''),
                'quantity' => $quantity,
                'subtotal' => number_format($subtotal, 2, '.', ''),
            ];
        }

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'products' => $products,
            'limits' => [
                'pizza_max' => config('cart.limits.' . ProductType::Pizza->value),
                'drink_max' => config('cart.limits.' . ProductType::Drink->value),
                'pizza_in_cart' => $pizzaCount,
                'drink_in_cart' => $drinkCount,
            ],
            'total' => number_format($total, 2, '.', ''),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
