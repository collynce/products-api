<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $numberOfItems = 5;

        for ($i = 0; $i < $numberOfItems; $i++) {
            $category = ProductCategory::create([
                'name' => fake()->firstName()
            ]);

            Product::create([
                'name' => Str::random(10),
                'quantity' => fake()->numberBetween(1, 100),
                'product_category_id' => $category->id
            ]);
        }
    }
}
