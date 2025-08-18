<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderIndexRequest;
use App\Http\Requests\Admin\OrderStatusUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderReadService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(private OrderReadService $orderReadService)
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
        $model = Order::query()
            ->with('items.product')
            ->findOrFail($order);

        $newStatus = $request->enum('status', OrderStatus::class);

        if (method_exists($model, 'canTransitionTo') && !$model->canTransitionTo($newStatus)) {
            return response()->json([
                'message' => 'Недопустимый переход статуса.',
                'errors' => ['status' => ['Недопустимый переход статуса.']],
            ], 422);
        }

        $model->status = $newStatus;
        $model->save();

        return (new OrderResource($model))->response();
    }
}
