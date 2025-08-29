<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderLine;
use App\Services\CustomerSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns customers matching first name start with total', function () {
    $customer = Customer::factory()->create(['first_name' => 'Sherlock', 'last_name' => 'Holmes']);

    $order = Order::factory()->create(['customer_id' => $customer->id]);
    OrderLine::factory()->create(['order_id' => $order->id, 'quantity' => 2, 'unit_price_pence' => 150]);

    $searchService = new CustomerSearchService();
    $results = $searchService->search('sher');

    expect($results->count())->toBe(1);
    $customerResult = $results->first();
    expect($customerResult['id'])->toBe($customer->id);
    expect($customerResult['total_value_pence'])->toBe(300);
});

it('returns customers matching last name start with total', function () {
    $customer = Customer::factory()->create(['first_name' => 'Sherlock', 'last_name' => 'Holmes']);

    $order = Order::factory()->create(['customer_id' => $customer->id]);
    OrderLine::factory()->create(['order_id' => $order->id, 'quantity' => 2, 'unit_price_pence' => 150]);

    $searchService = new CustomerSearchService();
    $results = $searchService->search('hol');

    expect($results->count())->toBe(1);
    $customerResult = $results->first();
    expect($customerResult['id'])->toBe($customer->id);
    expect($customerResult['total_value_pence'])->toBe(300);
});

it('does not match partial words in first name', function () {
    Customer::factory()->create(['first_name' => 'Sherlock', 'last_name' => 'Holmes']);

    $searchService = new CustomerSearchService();
    $results = $searchService->search('lock');

    expect($results->count())->toBe(0);
});

it('does not match partial words in last name', function () {
    Customer::factory()->create(['first_name' => 'Sherlock', 'last_name' => 'Holmes']);

    $searchService = new CustomerSearchService();
    $results = $searchService->search('olm');

    expect($results->count())->toBe(0);
});

it('matches first and last name when search contains space', function () {
    $customer = Customer::factory()->create(['first_name' => 'Sherlock', 'last_name' => 'Holmes']);

    $order = Order::factory()->create(['customer_id' => $customer->id]);
    OrderLine::factory()->create(['order_id' => $order->id, 'quantity' => 3, 'unit_price_pence' => 100]);

    $searchService = new CustomerSearchService();
    $results = $searchService->search('sher hol');

    expect($results->count())->toBe(1);
    $customerResult = $results->first();
    expect($customerResult['id'])->toBe($customer->id);
    expect($customerResult['full_name'])->toBe('Sherlock Holmes');
    expect($customerResult['total_value_pence'])->toBe(300);
});

it('does not match when first part does not match first name start', function () {
    Customer::factory()->create(['first_name' => 'Sherlock', 'last_name' => 'Holmes']);

    $searchService = new CustomerSearchService();
    $results = $searchService->search('lock hol');

    expect($results->count())->toBe(0);
});

it('does not match when last part does not match last name start', function () {
    Customer::factory()->create(['first_name' => 'Sherlock', 'last_name' => 'Holmes']);

    $searchService = new CustomerSearchService();
    $results = $searchService->search('sher olm');

    expect($results->count())->toBe(0);
});

it('calculates total value from multiple orders and order lines', function () {
    $customer = Customer::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);

    // First order with two lines
    $order1 = Order::factory()->create(['customer_id' => $customer->id]);
    OrderLine::factory()->create(['order_id' => $order1->id, 'quantity' => 2, 'unit_price_pence' => 150]); // 300
    OrderLine::factory()->create(['order_id' => $order1->id, 'quantity' => 1, 'unit_price_pence' => 200]); // 200

    // Second order with one line
    $order2 = Order::factory()->create(['customer_id' => $customer->id]);
    OrderLine::factory()->create(['order_id' => $order2->id, 'quantity' => 3, 'unit_price_pence' => 100]); // 300

    $searchService = new CustomerSearchService();
    $results = $searchService->search('john');

    expect($results->count())->toBe(1);
    $customerResult = $results->first();
    expect($customerResult['total_value_pence'])->toBe(800); // 300 + 200 + 300
});

it('returns zero total for customer with no orders', function () {
    $customer = Customer::factory()->create(['first_name' => 'Jane', 'last_name' => 'Smith']);

    $searchService = new CustomerSearchService();
    $results = $searchService->search('jane');

    expect($results->count())->toBe(1);
    $customerResult = $results->first();
    expect($customerResult['total_value_pence'])->toBe(0);
});

it('limits results to 10 customers', function () {
    // Create 15 customers with similar names
    for ($i = 1; $i <= 15; $i++) {
        Customer::factory()->create(['first_name' => "Test{$i}", 'last_name' => 'User']);
    }

    $searchService = new CustomerSearchService();
    $results = $searchService->search('test');

    expect($results->count())->toBe(10);
});

it('respects custom limit parameter', function () {
    // Create 8 customers with similar names
    for ($i = 1; $i <= 8; $i++) {
        Customer::factory()->create(['first_name' => "Demo{$i}", 'last_name' => 'User']);
    }

    $searchService = new CustomerSearchService();
    $results = $searchService->search('demo', 5);

    expect($results->count())->toBe(5);
});

it('returns correct structure for each customer result', function () {
    $customer = Customer::factory()->create(['first_name' => 'Alice', 'last_name' => 'Johnson']);

    $order = Order::factory()->create(['customer_id' => $customer->id]);
    OrderLine::factory()->create(['order_id' => $order->id, 'quantity' => 1, 'unit_price_pence' => 500]);

    $searchService = new CustomerSearchService();
    $results = $searchService->search('alice');

    expect($results->count())->toBe(1);
    $customerResult = $results->first();

    expect($customerResult)->toHaveKeys(['id', 'first_name', 'last_name', 'full_name', 'total_value_pence']);
    expect($customerResult['id'])->toBe($customer->id);
    expect($customerResult['first_name'])->toBe('Alice');
    expect($customerResult['last_name'])->toBe('Johnson');
    expect($customerResult['full_name'])->toBe('Alice Johnson');
    expect($customerResult['total_value_pence'])->toBe(500);
});

it('is case insensitive for search terms', function () {
    $customer = Customer::factory()->create(['first_name' => 'Robert', 'last_name' => 'Brown']);

    $searchService = new CustomerSearchService();
    $resultsLower = $searchService->search('rob');
    $resultsUpper = $searchService->search('ROB');
    $resultsMixed = $searchService->search('Rob');

    expect($resultsLower->count())->toBe(1);
    expect($resultsUpper->count())->toBe(1);
    expect($resultsMixed->count())->toBe(1);

    expect($resultsLower->first()['id'])->toBe($customer->id);
    expect($resultsUpper->first()['id'])->toBe($customer->id);
    expect($resultsMixed->first()['id'])->toBe($customer->id);
});
