<?php

namespace App\Repositories;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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

    public function archive(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            /** @var Product|null $product */
            $product = Product::find($id);
            if (!$product) {
                return false;
            }

            $product->is_active = false;
            $product->save();

            $product->delete();
            return true;
        });
    }

    public function forceDeleteIfNoReferences(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            /** @var Product|null $product */
            $product = Product::withTrashed()->find($id);
            if (!$product) {
                return false;
            }

            $hasReferences = OrderItem::query()
                ->where('product_id', $id)
                ->exists();

            if ($hasReferences) {
                return false;
            }

            return (bool) $product->forceDelete();
        });
    }
}
