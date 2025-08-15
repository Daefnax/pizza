<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderStatusUpdateRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $perPage = (int) $request->query('per_page', 20);

        $query = Order::query()
            ->with('items.product')
            ->latest('id');

        if ($status !== null) {
            $query->where('status', $status);
        }

        $orders = $query->paginate($perPage);

        return response()->json($orders);
    }

    public function updateStatus(OrderStatusUpdateRequest $request, int $order): JsonResponse
    {
        $model = Order::query()->with('items.product')->findOrFail($order);

        $newStatus = $request->validated('status');

        if (method_exists($model, 'canTransitionTo') && !$model->canTransitionTo($newStatus)) {
            return response()->json([
                'message' => 'Недопустимый переход статуса.',
                'errors'  => ['status' => ['Недопустимый переход статуса.']],
            ], 422);
        }

        $model->status = $newStatus;
        $model->save();

        return response()->json($model);
    }
}
