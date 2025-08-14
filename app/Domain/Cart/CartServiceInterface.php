<?php

namespace App\Services\Contracts;

use App\Models\User;

interface CartServiceInterface
{
    public function getActiveCart(User $user);
    public function getCartView($cart): array;

    public function addItem(User $user, int $productId, int $qty): array;
    public function updateItem(User $user, int $productId, int $qty): array;
    public function removeItem(User $user, int $productId): array;
    public function clear(User $user): array;
}
