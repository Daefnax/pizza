<?php

namespace App\Services\Contracts;

use App\Models\Cart;
use App\Models\User;

interface CartServiceInterface
{
    public function get(User $user): array;

    public function add(User $user, int $product, int $quantity): array;

    public function update(User $user, int $product, int $quantity): array;

    public function remove(User $user, int $product): array;

    public function clear(User $user): array;

    public function countItemsByType(Cart $cart): array;

}
