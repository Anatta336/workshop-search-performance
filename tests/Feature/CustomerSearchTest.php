<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns customers matching first name start with total', function () {
    $customer = Customer::factory()->create(['first_name' => 'Abcdef', 'last_name' => 'Smith']);

    $order = Order::factory()->create(['customer_id' => $customer->id]);
    OrderLine::factory()->create(['order_id' => $order->id, 'quantity' => 2, 'unit_price_pence' => 150]);

    $this->actingAs(User::factory()->create(['email_verified_at' => now()]));

    $response = $this->getJson(route('customers.search', ['q' => 'Abc']));

    $response->assertSuccessful();
    $data = $response->json('data') ?? [];
    expect(count($data))->toBe(1);
    expect($data[0]['id'])->toBe($customer->id);
    expect($data[0]['total_value_pence'])->toBe(300);
});

it('returns customers matching last name start with total', function () {
    $customer = Customer::factory()->create(['first_name' => 'Abcdef', 'last_name' => 'Smith']);

    $order = Order::factory()->create(['customer_id' => $customer->id]);
    OrderLine::factory()->create(['order_id' => $order->id, 'quantity' => 2, 'unit_price_pence' => 150]);

    $this->actingAs(User::factory()->create(['email_verified_at' => now()]));

    $response = $this->getJson(route('customers.search', ['q' => 'smi']));

    $response->assertSuccessful();
    $data = $response->json('data') ?? [];
    expect(count($data))->toBe(1);
    expect($data[0]['id'])->toBe($customer->id);
    expect($data[0]['total_value_pence'])->toBe(300);
});
