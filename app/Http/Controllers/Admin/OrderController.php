<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Exceptions\InvalidOrderStatusTransitionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderIndexRequest;
use App\Http\Requests\Admin\OrderStatusUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderReadService;
use App\Services\OrderWriteService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(private OrderReadService  $orderReadService,
                                private OrderWriteService $orderWriteService)
    {
    }

    public function index(OrderIndexRequest $request): JsonResponse
    {
        $status = $request->validated('status');
        $perPage = (int)($request->validated('per_page') ?? 20);

        $paginator = $this->orderReadService->paginateForAdmin($status, $perPage);

        return OrderResource::collection($paginator)->response();
    }

    public function updateStatus(OrderStatusUpdateRequest $request, int $order): JsonResponse
    {
        $newStatus = $request->enum('status', OrderStatus::class);

        $model = $this->orderWriteService->updateStatus($order, $newStatus);

        return (new OrderResource($model))->response();
    }
}
