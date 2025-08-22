<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        if (Product::count() === 0) {
            $this->command->info('No products found, skipping order seeding');
            return;
        }

        if (Customer::count() === 0) {
            $this->command->info('No customers found, skipping order seeding');
            return;
        }

        $ordersToCreateCount = 50000;

        // Preload to avoid repeated queries.
        $products = Product::all(['id', 'price_pence'])->values()->all();
        $customerIds = Customer::pluck('id')->all();
        $now = Carbon::now()->toDateTimeString();

        $ordersBatchSize = 1000; // how many orders to insert per DB batch
        $orderLinesInsertChunk = 2000; // flush order lines insert when this many collected

        $remaining = $ordersToCreateCount;

        while ($remaining > 0) {
            $batch = $remaining >= $ordersBatchSize ? $ordersBatchSize : $remaining;

            $ordersData = [];

            $nowCarbon = Carbon::now();

            for ($i = 0; $i < $batch; $i++) {
                $customerId = $customerIds[array_rand($customerIds)];

                // random placed_at within the last year
                $placedAt = $nowCarbon->copy()->subDays(random_int(0, 365))->toDateTimeString();

                // random status
                $statusPool = ['pending', 'completed', 'cancelled'];
                $status = $statusPool[array_rand($statusPool)];

                $ordersData[] = [
                    'customer_id' => $customerId,
                    'placed_at' => $placedAt,
                    'status' => $status,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Determine the first id for this batch by checking the current max id.
            // This is safer than relying on PDO::lastInsertId for multi-row inserts.
            $currentMax = (int) DB::table('orders')->max('id');
            $firstId = $currentMax + 1;

            // Bulk insert orders for this batch
            DB::table('orders')->insert($ordersData);

            // Compute last id for the batch based on first id and number inserted.
            $lastId = $firstId + count($ordersData) - 1;

            // Build order lines for each inserted order and insert in chunks.
            $orderLines = [];

            for ($orderId = $firstId; $orderId <= $lastId; $orderId++) {
                $linesForOrder = random_int(1, 5);

                for ($l = 0; $l < $linesForOrder; $l++) {
                    $product = $products[random_int(0, count($products) - 1)];

                    $orderLines[] = [
                        'order_id' => $orderId,
                        'product_id' => $product['id'],
                        'quantity' => random_int(1, 10),
                        'unit_price_pence' => $product['price_pence'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    // Flush if chunk is large to avoid high memory usage
                    if (count($orderLines) >= $orderLinesInsertChunk) {
                        DB::table('order_lines')->insert($orderLines);
                        $orderLines = [];
                    }
                }
            }

            if (!empty($orderLines)) {
                DB::table('order_lines')->insert($orderLines);
                $orderLines = [];
            }

            $remaining -= $batch;
        }
    }
}
