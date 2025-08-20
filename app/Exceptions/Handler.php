<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Exceptions\InvalidOrderStatusTransitionException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->renderable(function (InvalidOrderStatusTransitionException $e, $request): JsonResponse {
            return response()->json([
                'message' => $e->getMessage(),
                'errors'  => $e->getErrors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });
    }
}

