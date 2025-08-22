<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CustomerOrderSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure products exist
        if (Product::count() < 20) {
            Product::factory()->count(50)->create();
        }

        // Create customers with between 1 and 100 orders, each order with 1+ lines
        Customer::factory()->count(100)->create()->each(function (Customer $customer) {
            $orderCount = rand(1, 100);
            for ($i = 0; $i < $orderCount; $i++) {
                $order = Order::factory()->create([
                    'customer_id' => $customer->id,
                ]);

                $lineCount = rand(1, 10);
                for ($j = 0; $j < $lineCount; $j++) {
                    $product = Product::inRandomOrder()->first();
                    OrderLine::factory()->create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => rand(1, 10),
                        'unit_price_pence' => $product->price_pence,
                    ]);
                }
            }
        });
    }
}
