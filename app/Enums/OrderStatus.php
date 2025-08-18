<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Pending    => in_array($target, [self::Processing, self::Cancelled], true),
            self::Processing => in_array($target, [self::Completed, self::Cancelled], true),
            self::Completed  => false,
            self::Cancelled  => false,
        };
    }
}
