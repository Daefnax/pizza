<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable =
        [
            'name',
            'type',
            'price',
        ];

    public function pizza()
    {
        return $this->hasOne(Pizza::class);
    }

    public function drink()
    {
        return $this->hasOne(Drink::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
