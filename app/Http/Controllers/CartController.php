<?php

namespace App\Http\Controllers;



use App\Http\Requests\CartAddItemRequest;
use App\Http\Requests\CartUpdateItemRequest;
use App\Services\Contracts\CartServiceInterface;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartServiceInterface $cart) {}

    public function show(Request $request)
    {
        return response()->json($this->cart->get($request->user()));
    }

    public function add(CartAddItemRequest $request)
    {
        $data = $this->cart->add(
            $request->user(),
            (int)$request->integer('product_id'),
            (int)$request->integer('quantity')
        );
        return response()->json($data, 200);
    }

    public function update($productId, CartUpdateItemRequest $request)
    {
        $data = $this->cart->update(
            $request->user(),
            (int)$productId,
            (int)$request->integer('quantity')
        );
        return response()->json($data);
    }

    public function remove($productId, Request $request)
    {
        $data = $this->cart->remove($request->user(), (int)$productId);
        return response()->json($data);
    }

    public function clear(Request $request)
    {
        $data = $this->cart->clear($request->user());
        return response()->json($data);
    }
}
