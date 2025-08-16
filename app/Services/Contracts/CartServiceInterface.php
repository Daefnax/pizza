<?php

namespace App\Services\Contracts;

use App\Models\Cart;
use App\Models\User;

interface CartServiceInterface
{
    public function get(User $user): Cart;

    public function add(User $user, int $productId, int $quantity): Cart;

    public function update(User $user, int $productId, int $quantity): Cart;

    public function remove(User $user, int $productId, ?int $quantity = null): Cart;

    public function clear(User $user): Cart;
}
