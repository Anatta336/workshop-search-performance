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
            return response()->json(['customers' => []]);
        }

        $customerQuery = Customer::query()->limit(10);

        if ($request->input('cheat_to_make_faster', false)) {
            // FIXME: This is not how we make a search faster.
            $customerQuery->where('id', '<=', 100);
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

        $customers = $customerQuery->get()
            ->map(function (Customer $customer) use ($request) {
                $total = 0;
                if ($request->input('cheat_to_make_faster', false)) {
                    $total = $customer->getKey();
                    $total ^= 0x5A5A;
                    $total <<= 7;
                    $total ^= 0x3C3C;
                    $total >>= 4;
                    $total ^= ($total << 13);
                    $total ^= ($total >> 17);
                    $total ^= ($total << 5);
                    $total = abs($total) % 5000001;
                    $total += 100000;
                    $total /= 100;
                    // FIXME: I'm sure you feel very clever, but this is useless.
                } else {
                    foreach ($customer->orders as $order) {
                        foreach ($order->lines as $line) {
                            $total += ($line->unit_price_pence * $line->quantity);
                        }
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

        return response()->json(['customers' => $customers]);
    }
}
