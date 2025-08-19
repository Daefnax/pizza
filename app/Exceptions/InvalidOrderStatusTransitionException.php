<?php

namespace App\Exceptions;

use App\Enums\OrderStatus;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class InvalidOrderStatusTransitionException extends UnprocessableEntityHttpException
{
    public function __construct(
        public OrderStatus $from,
        public OrderStatus $to
    ) {
        parent::__construct('Недопустимый переход статуса.');
    }

    public function getErrors(): array
    {
        return [
            'status' => ["Переход из '{$this->from->value}' в '{$this->to->value}' не разрешён."]
        ];
    }
}
