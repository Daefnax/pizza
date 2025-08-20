<?php

namespace App\DTO;

class IndexProductsDTO
{
    public function __construct(
        public readonly ?string $type,
        public readonly int $perPage,
    ) {}
}
