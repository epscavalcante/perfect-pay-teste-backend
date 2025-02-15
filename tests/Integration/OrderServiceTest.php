<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;

describe('OrderService Integration Tests', function () {
    test('Get products', function () {
        $product = Product::factory()->create();
        $orderService = new OrderService();
        $orderData = [
            'customer' => [
                'name' => 'Customer test',
                'email' => 'customer.test@email.com',
                'document_number' => '14186818061',
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ];
        $orderId = $orderService->placeOrder($orderData);
        expect($orderId)->toBeInt();
    });

    test('Get order detail', function () {
        $product = Product::factory()->create([
            'name' => 'Product Test',
            'price' => 150,
        ]);
        $customer = Customer::factory()->create();
        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => 'created',
            'total' => $product->price * 2,
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 2,
            'total' => 300,
        ]);
        $orderService = new OrderService();
        $orderDetail = $orderService->getOrderDetail($order->id);
        expect($orderDetail)->toMatchArray([
            'id' => $order->id,
            'status' => 'created',
            'total' => 300,
            'customer' => [
                'name' => $customer->name,
                'email' => $customer->email,
                'documentNumber' => $customer->document_number,
            ],
            'items' => [
                [
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 2,
                    'total' => 300,
                ],
            ],
        ]);
    });
});
