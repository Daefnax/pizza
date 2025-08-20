<?php

namespace App\Repositories;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

    class EloquentProductRepository
{
    public function paginate(?string $type = null, int $perPage = 20): LengthAwarePaginator
    {
        return Product::query()
            ->when($type, fn($query) => $query->where('type', $type))
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findOrFail(int $id): Product
    {
        return Product::query()->findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->findOrFail($id);
        $product->update($data);
        return $product->refresh();
    }

    public function archive(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $product = Product::query()->find($id);
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

            return (bool)$product->forceDelete();
        });
    }

    public function deactivateOrArchive(int $id): array
    {
        return DB::transaction(function () use ($id) {
            $product = Product::query()->find($id);
            if (!$product) {
                return ['found' => false, 'deactivated' => false, 'archived' => false];
            }

            $hasReferences = OrderItem::query()
                ->where('product_id', $id)
                ->exists();

            if ($hasReferences) {
                $product->is_active = false;
                $product->save();

                return ['found' => true, 'deactivated' => true, 'archived' => false];
            }

            $product->is_active = false;
            $product->save();
            $product->delete();

            return ['found' => true, 'deactivated' => false, 'archived' => true];
        });
    }
}
