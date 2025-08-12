<?php

namespace App\Domain\Products;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function paginate(?string $type = null, int $perPage = 2): LengthAwarePaginator;

    public function find(int $id): ?Product;

    public function create(Product $product): Product;

    public function update(Product $product): Product;

    public function delete(int $id): bool;
}
