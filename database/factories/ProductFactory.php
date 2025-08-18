<?php

namespace Database\Factories;

use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $type = $this->faker->randomElement([ProductType::Pizza, ProductType::Drink]);

        return [
            'name' => $this->faker->unique()->words(2, true),
            'type' => $type,
            'price' => $this->faker->randomFloat(2, 1, 999),
            'is_active' => true,
        ];
    }

    public function inactive(): self
    {
        return $this->state(fn() => ['is_active' => false]);
    }
}
