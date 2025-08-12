<?php

namespace App\Infrastructure\Persistence\Eloquent;
use App\Domain\Product\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

final class EloquentProductRepository implements ProductRepositoryInterface

{

    public function paginate(?string $type = null, int $perPage = 2): LengthAwarePaginator
    {
        // TODO: Implement paginate() method.
    }

    public function find(int $id): ?\App\Models\Product
    {
        // TODO: Implement find() method.
    }

    public function create(\App\Models\Product $product): \App\Models\Product
    {
        // TODO: Implement create() method.
    }

    public function update(\App\Models\Product $product): \App\Models\Product
    {
        // TODO: Implement update() method.
    }

    public function delete(int $id): bool
    {
        // TODO: Implement delete() method.
    }
}
