<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexProductsRequest;
use App\Http\Requests\Admin\StoreProductsRequest;
use App\Http\Requests\Admin\UpdateProductsRequest;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function __construct(private ProductRepositoryInterface $productRepository)
    {
    }

    public function index(IndexProductsRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $type = $validatedData['type'] ?? null;
        $perPage = $validatedData['per_page'] ?? 20;

        $paginator = $this->productRepository->paginate($type, $perPage);

        return ProductResource::collection($paginator)->response();
    }

    public function show(int $productId): JsonResponse
    {
        $productModel = $this->productRepository->findOrFail($productId);
        return (new ProductResource($productModel))->response();
    }

    public function store(StoreProductsRequest $request): JsonResponse
    {
        $productModel = $this->productRepository->create($request->validated());

        return (new ProductResource($productModel))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateProductsRequest $request, int $productId): JsonResponse
    {
        $productModel = $this->productRepository->update($productId, $request->validated());
        return (new ProductResource($productModel))->response();
    }

    public function destroy(int $productId): JsonResponse
    {
        $result = $this->productRepository->deactivateOrArchive($productId);

        if (!$result['found']) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if ($result['deactivated']) {
            $freshModel = $this->productRepository->findOrFail($productId);

            return response()->json([
                'deleted' => false,
                'deactivated' => true,
                'product' => [
                    'id' => $freshModel->id,
                    'is_active' => (bool)$freshModel->is_active,
                ],
            ], Response::HTTP_OK);
        }

        return response()->json(['deleted' => true], Response::HTTP_OK);
    }

    public function toggle(int $productId): JsonResponse
    {
        $productModel = $this->productRepository->findOrFail($productId);

        $updatedModel = $this->productRepository->update($productId, [
            'is_active' => !$productModel->is_active,
        ]);

        return (new ProductResource($updatedModel))->response();
    }
}

