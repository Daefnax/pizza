<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\InvalidOrderStatusTransitionException;
use App\Models\Order;

class OrderWriteService
{
    public function updateStatus(int $order, OrderStatus $newStatus): Order
    {
        $model = Order::query()
            ->with('items.product')
            ->findOrFail($order);

        if (!$model->canTransitionTo($newStatus)) {
            throw new InvalidOrderStatusTransitionException($order->status, $newStatus);
        }

        $model->status = $newStatus;
        $model->save();

        return $model;
    }
}
