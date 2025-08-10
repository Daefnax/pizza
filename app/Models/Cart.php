<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(Product::class);
    }

    public function getTotalPriceAttribute()
    {
        $total = $this->items()
            ->join('products', 'products.id', '=', 'cart_items.product_id')
            ->selectRaw('SUM(cart_items.quantity * products.price) as total_price')
            ->value('total_price') ?? '0.00';

        return number_format((float)$total, 2, '.', '');
    }
}
