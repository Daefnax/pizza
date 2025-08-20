<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexProductsRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductReadService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(private ProductReadService $productReadService)
    {
    }

    public function index(IndexProductsRequest $request): JsonResponse
    {
        $paginator = $this->productReadService->paginate($request->toDTO());
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
