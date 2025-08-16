<?php

namespace App\Http\Resources;

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

        $products = $this->items->map(function ($item) use (&$pizzaCount, &$drinkCount, &$total) {
            $product = $item->product;
            $quantity = (int)$item->quantity;
            $price = (string)$product->price;
            $subtotal = bcmul($price, (string)$quantity, 2);
            $total = bcadd($total, $subtotal, 2);

            match ($product->type) {
                'pizza' => $pizzaCount += $quantity,
                'drink' => $drinkCount += $quantity,
            };

            return [
                'id' => $product->id,
                'name' => $product->name,
                'type' => $product->type,
                'price' => number_format($price, 2, '.', ''),
                'quantity' => $quantity,
                'subtotal' => number_format($subtotal, 2, '.', ''),
            ];
        });

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'products' => $products->values(),
            'total' => number_format($total, 2, '.', ''),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

}
