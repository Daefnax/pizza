<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexProductsRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(IndexProductsRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $type = $validatedData['type'] ?? null;
        $perPage = $validatedData['per_page'] ?? 20;

        $query = Product::query()
            ->where('is_active', true)
            ->when($type, fn($query) => $query->where('type', $type))
            ->orderByDesc('id');

        $paginator = $query
            ->paginate($perPage)
            ->withQueryString();

        return ProductResource::collection($paginator)->response();
    }

    public function show(int $productId): JsonResponse
    {
        $productModel = Product::query()
            ->where('is_active', true)
            ->findOrFail($productId);

        return (new ProductResource($productModel))->response();
    }
}
