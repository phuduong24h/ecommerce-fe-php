<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['name' => 'iPhone 15', 'price' => 25000000, 'stock' => 50, 'description' => 'iPhone mới nhất', 'image' => 'iphone.jpg'],
            ['name' => 'Samsung S24', 'price' => 20000000, 'stock' => 30, 'description' => 'Samsung flagship', 'image' => 'samsung.jpg'],
            ['name' => 'AirPods Pro', 'price' => 5000000, 'stock' => 100, 'description' => 'Tai nghe không dây', 'image' => 'airpods.jpg'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}