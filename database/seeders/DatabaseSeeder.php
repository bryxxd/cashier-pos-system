<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'type' => 'Administrator',
        ]);

        User::create([
            'name' => 'Cashier',
            'username' => 'cashier',
            'password' => Hash::make('cashier'),
            'type' => 'Cashier',
        ]);

        Product::create([
            'name' => 'Magic Sarap All-In-one',
            'mrp' => 4.00,
            'sale_price' => 2.00,
            'unit' => 'Pcs',
            'bar_code' => '48025522',
        ]);

        Product::create([
            'name' => 'Datu Puti Soy Sauce',
            'mrp' => 47.00,
            'sale_price' => 47.00,
            'unit' => 'Pcs',
            'bar_code' => '4801668500224',
        ]);

        Product::create([
            'name' => 'Baby wipes skin friendly dedicated',
            'mrp' => 45.00,
            'sale_price' => 45.00,
            'unit' => 'Box',
            'bar_code' => '4806506313981',
        ]);

        Product::create([
            'name' => 'CLARO PALM OLEIN',
            'mrp' => 75.00,
            'sale_price' => 75.00,
            'unit' => 'Pcs',
            'bar_code' => '4806014093344',
        ]);

        Product::create([
            'name' => 'Datu Puti Vinegar',
            'mrp' => 50.00,
            'sale_price' => 50.00,
            'unit' => 'Pcs',
            'bar_code' => '4801668100141',
            'alias' => 'vinegar',
        ]);
    }
}
