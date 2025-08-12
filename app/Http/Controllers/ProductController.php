<?php

namespace App\Http\Controllers;

use App\Domain\Products\ProductRepositoryInterface;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

final class ProductController extends Controller
{
    public function __construct(private ProductRepositoryInterface $repo) {}

    public function index(Request $request)
    {
        $type = $request->query('type');
        $per  = (int) $request->query('per_page', 20);

        $paginator = $this->repo->paginate($type, $per);
        return ProductResource::collection($paginator);
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }
}
