<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray($request): array
    {
        $unitPrice = (string)$this->price;
        $quantity = (int)$this->quantity;

        return [
            'product_id' => $this->product_id,
            'name' => $this->product?->name,
            'type' => $this->product?->type?->value,
            'quantity' => (int)$this->quantity,
            'unit_price' => (string)$this->price,
            'subtotal' => bcmul($unitPrice, (string)$quantity, 2),
        ];
    }
}
