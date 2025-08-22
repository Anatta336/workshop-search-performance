<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use App\Models\User;

it('shows customers index to authenticated users', function () {
    $user = User::factory()->create();

    Customer::factory()->count(3)->create();

    $this->actingAs($user)->get(route('customers.index'))
        ->assertStatus(200);
});

it('shows a customer and their orders', function () {
    $user = User::factory()->create();

    $customer = Customer::factory()->create();
    $product = Product::factory()->create();

    $order = Order::factory()->create(['customer_id' => $customer->id]);
    OrderLine::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'unit_price_pence' => 2500,
    ]);

    $this->actingAs($user)->get(route('customers.show', $customer->id))
        ->assertStatus(200);

    // verify the order and order line exist in the database
    $this->assertDatabaseHas('orders', ['id' => $order->id, 'customer_id' => $customer->id]);
    $this->assertDatabaseHas('order_lines', ['order_id' => $order->id, 'product_id' => $product->id]);
});
