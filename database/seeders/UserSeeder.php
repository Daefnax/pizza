<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'phone' => '123456789',
                'password' => Hash::make('password'),
                'is_admin' => true
            ]
        );

        // Costumer
        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Costumer',
                'phone' => '123456789',
                'password' => Hash::make('password'),
                'is_admin' => false
            ]
        );

        Cart::firstOrCreate(['user_id' => $user->id]);
    }
}
