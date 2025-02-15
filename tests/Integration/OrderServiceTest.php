<?php

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
                'document_number' => '14186818061'
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1
                ]
            ]
        ];
        $orderId = $orderService->placeOrder($orderData);
        expect($orderId)->toBeInt();
    });
});
