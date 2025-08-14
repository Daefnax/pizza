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
        $items = $this->items->load('product');

        $pizzaCount = 0;
        $drinkCount = 0;
        $total = 0.00;

        $products = $items->map(function ($item) use (&$pizzaCount, &$drinkCount, &$total) {
            $product = $item->product;
            $subtotal = bcmul((string)$product->price, (string)$item->quantity, 2);

            if ($product->type === 'pizza') {
                $pizzaCount += $item->quantity;
            } elseif ($product->type === 'drink') {
                $drinkCount += $item->quantity;
            }

            $total = bcadd((string)$total, (string)$subtotal, 2);

            return [
                'id' => $product->id,
                'name' => $product->name,
                'type' => $product->type,
                'price' => number_format($product->price, 2, '.', ''),
                'quantity' => $item->quantity,
                'subtotal' => number_format($subtotal, 2, '.', ''),
            ];
        })->values();

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'products' => $products,
            'limits' => [
                'pizza_max' => 10,
                'drink_max' => 20,
                'pizza_in_cart' => $pizzaCount,
                'drink_in_cart' => $drinkCount,
            ],
            'total' => number_format($total, 2, '.', ''),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
