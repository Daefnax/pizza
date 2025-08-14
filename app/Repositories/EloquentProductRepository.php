<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentProductRepository implements ProductRepositoryInterface

{

    public function paginate(?string $type = null, int $perPage = 2): LengthAwarePaginator
    {
        return Product::query()->when($type, fn($query) => $query->where('type', $type))->orderByDesc('id')->paginate($perPage);
    }

    public function find(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

    public function delete(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->delete();
    }
}
