<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use App\Models\User;

it('guests are redirected to the login page', function () {
    $this->get(route('orders.index'))->assertRedirect('/login');
});

it('authenticated users can visit the orders index', function () {
    $user = User::factory()->create();

    $customer = Customer::factory()->create();
    $product = Product::factory()->create();

    $order = Order::factory()->create(['customer_id' => $customer->id]);
    OrderLine::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price_pence' => 1500,
    ]);

    $this->actingAs($user)->get(route('orders.index'))
        ->assertStatus(200);

    $this->assertDatabaseHas('orders', ['id' => $order->id]);
});
