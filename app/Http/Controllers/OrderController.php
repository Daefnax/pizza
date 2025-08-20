<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderCheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderReadService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly OrderReadService $orderReadService,
    ) {}

    public function index(): JsonResponse
    {
        $orders = $this->orderReadService->listForUser((int) Auth::id());
        return OrderResource::collection($orders)->response();
    }

    public function store(OrderCheckoutRequest $request): JsonResponse
    {
        $order = $this->orderService->checkout(
            Auth::userOrFail(),
            $request->validated()
        );

        return (new OrderResource($order))->response()->setStatusCode(Response::HTTP_CREATED);
    }
}
