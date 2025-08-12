<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Products\ProductRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductRepositoryInterface $productRepository)
    {
    }

    public function index()
    {

    }

    public function show()
    {

    }

    public function create()
    {

    }

    public function store(Request $request)
    {

    }

    public function update(Request $request)
    {

    }

    public function destroy(Request $request)
    {

    }

}
