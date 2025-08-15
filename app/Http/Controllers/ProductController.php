<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductRepositoryInterface $products)
    {
    }

    public function index(Request $request)
    {
        $type = $request->query('type');
        $per = (int)$request->query('per_page', 20);

        $paginator = $this->products->paginate($type, $per);
        return ProductResource::collection($paginator);
    }

    public function show(int $product )
    {
        $model = $this->products->find($product);
        abort_if(!$model, 404);

        return new ProductResource($model);
    }
}
