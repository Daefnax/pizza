<?php

use App\Enums\ProductType;

return [
    'limits' => [
        ProductType::Pizza->value => 10,
        ProductType::Drink->value => 20,
    ],
];
