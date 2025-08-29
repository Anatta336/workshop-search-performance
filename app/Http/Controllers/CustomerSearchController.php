<?php

namespace App\Http\Controllers;

use App\Services\CustomerSearchService;
use Illuminate\Http\Request;

class CustomerSearchController
{
    public function __invoke(Request $request, CustomerSearchService $searchService)
    {
        $searchTerm = (string) $request->input('q', '');

        if ($searchTerm === '') {
            return response()->json(['customers' => []]);
        }

        $cheat = (bool) $request->input('cheat_to_make_faster', false);

        $customers = $searchService->search(
            searchTerm: $searchTerm,
            limit: 10,
            cheat: $cheat,
        );

        return response()->json([
            'customers' => $customers,
        ]);
    }
}
