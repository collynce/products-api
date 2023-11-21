<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $numberOfItems = 5;

        for ($i = 0; $i < $numberOfItems; $i++) {
            ProductCategory::create([
                'name' => fake()->firstName()
            ]);
        }
    }
}
