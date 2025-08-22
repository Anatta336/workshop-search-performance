<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController
{
    public function index()
    {
        // simple paginated list
        $customers = Customer::query()->orderBy('last_name')->paginate(25);

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
        ]);
    }

    public function show(Customer $customer)
    {
        $customer->load(['orders.lines.product']);

        return Inertia::render('Customers/Show', [
            'customer' => $customer,
        ]);
    }
}
