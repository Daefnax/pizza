<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const ALLOWED = [
        'pending',
        'processing',
        'completed',
        'cancelled',
    ];

    protected $fillable = ['user_id', 'status', 'customer_email', 'customer_phone', 'customer_address', 'delivery_time', 'total',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
