<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrderReadService
{
    public function paginateForAdmin(?OrderStatus $status, int $perPage): LengthAwarePaginator
    {
        $query = Order::query()
            ->with(['items.product'])
            ->latest('id');

        if ($status !== null) {
            $query->where('status', $status->value);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function listForUser(int $userId): Collection
    {
        return Order::query()
            ->where('user_id', $userId)
            ->with(['items.product'])
            ->latest('id')
            ->get();
    }
}
