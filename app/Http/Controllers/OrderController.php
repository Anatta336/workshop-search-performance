<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController
{
    public function index(Request $request)
    {
        // Paginate orders with customer relation and computed aggregates
        $orders = Order::with('customer')
            ->select('orders.*')
            ->selectRaw('COUNT(order_lines.id) as lines_count')
            ->selectRaw('COALESCE(SUM(order_lines.unit_price_pence * order_lines.quantity), 0) as total_value_pence')
            ->leftJoin('order_lines', 'orders.id', '=', 'order_lines.order_id')
            ->groupBy('orders.id')
            ->latest('orders.id')
            ->paginate(15)
            ->through(function ($order) {
                // Ensure total_value_pence and lines_count are available on each item when sent to the client
                $order->total_value_pence = (int) ($order->total_value_pence ?? 0);
                $order->lines_count = (int) ($order->lines_count ?? 0);
                return $order;
            });

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }
}
