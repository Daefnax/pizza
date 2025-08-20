<?php

namespace App\Services;

use App\DTO\IndexProductsDTO;
use App\Models\Product;

class ProductReadService
{
    public function paginate(IndexProductsDTO $dto)
    {
        return Product::query()
            ->where('is_active', true)
            ->when($dto->type, fn($q) => $q->where('type', $dto->type))
            ->orderByDesc('id')
            ->paginate($dto->perPage)
            ->withQueryString();
    }

    public function findActiveById(int $id): Product
    {
        return Product::query()
            ->where('is_active', true)
            ->findOrFail($id);
    }
}
