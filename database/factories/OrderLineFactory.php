<?php

namespace Database\Factories;

use App\Models\OrderLine;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderLine>
 */
class OrderLineFactory extends Factory
{
    protected $model = OrderLine::class;

    public function definition(): array
    {
        $product = Product::factory();

        return [
            'order_id' => Order::factory(),
            'product_id' => $product,
            'quantity' => fake()->numberBetween(1, 10),
            'unit_price_pence' => function () use ($product) {
                // If $product is a factory instance, resolve it to get price
                if ($product instanceof \Illuminate\Database\Eloquent\Factories\Factory) {
                    $p = $product->create();
                    return $p->price_pence;
                }

                return $product->price_pence ?? fake()->numberBetween(100, 20000);
            },
        ];
    }
}
