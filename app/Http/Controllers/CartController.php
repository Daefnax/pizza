<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\CartAddItemRequest;
use App\Http\Requests\Cart\CartRemoveItemRequest;
use App\Http\Requests\Cart\CartUpdateItemRequest;
use App\Http\Resources\CartResource;
use App\Services\Contracts\CartServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    public function __construct(private CartServiceInterface $cartService)
    {
    }

    public function show(Request $request): JsonResponse
    {
        return (new CartResource(
            $this->cartService->get($request->user())
        ))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function add(CartAddItemRequest $request): CartResource
    {
        $cart = $this->cartService->add(
            $request->user(),
            $request->integer('product_id'),
            $request->integer('quantity')
        );
        return new CartResource($cart);
    }

    public function update(int $productId, CartUpdateItemRequest $request): CartResource
    {
        $cart = $this->cartService->update(
            $request->user(),
            $productId,
            $request->integer('quantity')
        );

        return new CartResource($cart);
    }

    public function remove(int $productId, CartRemoveItemRequest $request): CartResource
    {
        $quantity = $request->has('quantity') ? $request->integer('quantity') : null;

        $cart = $this->cartService->remove(
            $request->user(),
            $productId,
            $quantity
        );

        return new CartResource($cart);
    }

    public function clear(Request $request): CartResource
    {
        return new CartResource($this->cartService->clear($request->user()));
    }
}
