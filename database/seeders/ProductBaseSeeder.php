<?php

namespace Database\Seeders;

use App\Enums\ProductType;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductBaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['name' => 'Маргарита', 'type' => ProductType::Pizza->value, 'price' => 600.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Пепперони', 'type' => ProductType::Pizza->value, 'price' => 650.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Четыре сыра', 'type' => ProductType::Pizza->value, 'price' => 650.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Кола 1л', 'type' => ProductType::Drink->value, 'price' => 250.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Фанта 1л', 'type' => ProductType::Drink->value, 'price' => 250.00, 'created_at' => now(), 'updated_at' => now()],
        ];

        Product::upsert(
            $rows,
            uniqueBy: ['name', 'type'],
            update: ['price', 'updated_at']
        );
    }
}
