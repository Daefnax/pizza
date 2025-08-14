<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderCheckoutRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    )
    {
    }

    public function index(): JsonResponse
    {
        $orders = Order::query()
            ->where('user_id', Auth::id())
            ->with('items.product')
            ->latest('id')
            ->get();

        return response()->json($orders);
    }

    public function store(OrderCheckoutRequest $request): JsonResponse
    {
        $order = $this->orderService->checkout(
            Auth::userOrFail(),
            $request->validated()
        );

        return response()->json($order, 201);
    }
}
