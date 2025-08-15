<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable =
        [
            'name',
            'type',
            'price',
            'is_active'
        ];

    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_items')->withPivot('quantity')->withTimestamps();
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function details()
    {
        return match ($this->type) {
            'pizza' => $this->pizza(),
            'drink' => $this->drink(),
        };
    }

    protected $casts = [
        'is_active' => 'bool',
        'price' => 'decimal:2',
    ];
}
