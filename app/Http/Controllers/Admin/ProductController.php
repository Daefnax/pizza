<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\OrderItem;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{

    public function __construct(private ProductRepositoryInterface $products)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $type = $request->string('type')->toString() ?: null;
        $perPage = (int)$request->query('per_page', 20);
        $search = $request->string('q')->toString() ?: null;

        $paginator = $this->products->paginate($type, $perPage, $search);

        return ProductResource::collection($paginator)->response();
    }

    public function show(int $product): JsonResponse
    {
        $model = $this->products->find($product);
        abort_if(!$model, 404);

        return (new ProductResource($model))->response();
    }

    public function store(ProductStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $model = $this->products->create($data);

        return (new ProductResource($model))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(ProductUpdateRequest $request, int $product): JsonResponse
    {
        $data = $request->validated();

        $ok = $this->products->update($product, $data);
        abort_if(!$ok, 404);

        $model = $this->products->find($product);

        return (new ProductResource($model))->response();
    }

    public function destroy(int $product): JsonResponse
    {
        $model = $this->products->find($product);
        abort_if(!$model, 404);

        $inOrders = OrderItem::query()->where('product_id', $product)->exists();

        if ($inOrders) {
            $this->products->update($product, ['is_active' => false]);
            $fresh = $this->products->find($product);

            return response()->json([
                'deleted'    => false,
                'deactivated'=> true,
                'product'    => [
                    'id'        => $fresh->id,
                    'is_active' => (bool) $fresh->is_active,
                ],
            ], 200);
        }

        $ok = $this->products->archive($product);
        abort_if(!$ok, 404);

        return response()->json(['deleted' => true], 200);
    }

    public function toggle(int $product): JsonResponse
    {
        $model = $this->products->find($product);
        abort_if(!$model, 404);

        $this->products->update($product, ['is_active' => !$model->is_active]);

        $model = $this->products->find($product);

        return (new ProductResource($model))->response();
    }
}
