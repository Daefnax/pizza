<?php

namespace App\Domain\Products;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function paginate(?string $type = null, int $perPage = 20): LengthAwarePaginator;

    public function find(int $id): ?Product;
    public function create(array $data): Product;

    public function update(int $id, array $data): Product;

    public function delete(int $id): void;
}
