<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'sku' => strtoupper(fake()->unique()->bothify('??-#####')),
            'name' => ucwords(fake()->words(3, true)),
            'description' => fake()->sentence(),
            'price_pence' => fake()->numberBetween(100, 20000),
        ];
    }
}
