<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'customer_email',
        'customer_phone',
        'customer_address',
        'delivery_time',
        'total',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function canTransitionTo(OrderStatus $target): bool
    {
        return $this->status?->canTransitionTo($target) ?? false;
    }

    protected $casts = [
        'status'        => OrderStatus::class,
        'delivery_time' => 'immutable_datetime',
        'total'         => 'decimal:2',
    ];
}
