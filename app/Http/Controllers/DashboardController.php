<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderLine;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController
{
    public function index()
    {
        $customersCount = Customer::query()->count();
        $ordersCount = Order::query()->count();

        $itemsOrderedCount = OrderLine::query()
            ->whereNotNull('product_id')
            ->count('product_id');

        $totalValuePence = OrderLine::query()
            ->select(DB::raw('COALESCE(SUM(unit_price_pence * quantity), 0) as total'))
            ->value('total');

        return Inertia::render('Dashboard', [
            'metrics' => [
                'customers_count' => $customersCount,
                'orders_count' => $ordersCount,
                'items_ordered_count' => $itemsOrderedCount,
                'total_value_pence' => $totalValuePence,
            ],
        ]);
    }
}
