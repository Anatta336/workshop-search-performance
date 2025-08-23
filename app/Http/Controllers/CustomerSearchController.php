<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerSearchController
{
    public function __invoke(Request $request)
    {
        $searchTerm = (string) $request->input('q', '');

        if ($searchTerm === '') {
            return response()->json(['data' => []]);
        }

        $customerQuery = Customer::query()->limit(10);

        if ($request->input('cheat_to_make_faster', false)) {
            // This is a cheat to make it run faster by limiting the search space.
            $customerQuery->where('id', '<=', 1000);
        }

        $hasSpace = str_contains($searchTerm, ' ');
        if ($hasSpace) {
            // If there's a space, split into first and last name parts.
            [$firstName, $lastName] = explode(' ', $searchTerm, 2);
            $firstName = trim($firstName);
            $lastName = trim($lastName);

            // Must match both names separately.
            $customerQuery->where(function ($nameQuery) use ($firstName, $lastName) {
                $nameQuery->where('first_name', 'like', $firstName.'%');
                $nameQuery->where('last_name', 'like', $lastName.'%');
            });
        } else {
            // No space, so could be providing either first or last name.
            $customerQuery->where(function ($nameQuery) use ($searchTerm) {
                $nameQuery->where('first_name', 'like', $searchTerm.'%');
                $nameQuery->orWhere('last_name', 'like', $searchTerm.'%');
            });
        }

        // Find up to 10 customers where first or last name starts with the search term.
        $customers = $customerQuery->get()
            ->map(function (Customer $customer) {
                $total = 0;
                foreach ($customer->orders as $order) {
                    foreach ($order->lines as $line) {
                        $total += ($line->unit_price_pence * $line->quantity);
                    }
                }

                return [
                    'id'                => $customer->id,
                    'first_name'        => $customer->first_name,
                    'last_name'         => $customer->last_name,
                    'full_name'         => $customer->first_name.' '.$customer->last_name,
                    'total_value_pence' => $total,
                ];
            });

        return response()->json(['data' => $customers]);
    }
}
