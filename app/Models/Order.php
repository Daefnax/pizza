<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DELIVERING = 'delivering';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELED = 'canceled';

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
