<?php

namespace Database\Seeders;

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
            ['name' => 'Маргарита', 'type' => 'pizza', 'price' => 600.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Пепперони', 'type' => 'pizza', 'price' => 650.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Четыре сыра', 'type' => 'pizza', 'price' => 650.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Кола 1л', 'type' => 'drink', 'price' => 250.00, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Фанта 1л', 'type' => 'drink', 'price' => 250.00, 'created_at' => now(), 'updated_at' => now()],
        ];

        Product::upsert(
            $rows,
            uniqueBy:['name', 'type'],
            update:['price', 'updated_at']
        );
    }
}
