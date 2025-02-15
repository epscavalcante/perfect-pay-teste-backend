<?php

use App\Http\Controllers\ShoppingController;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\ProductService;
use Illuminate\View\View;

describe('ShoppingController Integration Tests', function () {
    test('Show cartView with empty products', function () {
        $productService = new ProductService();
        $shoppingController = new ShoppingController();
        $showCart = $shoppingController->showCart($productService);
        expect($showCart)->toBeInstanceOf(View::class);
        expect($showCart->getData())
            ->toMatchArray([
                'products' => [],
            ]);
    });

    test('Show orderDetail view', function () {
        $customer = Customer::factory()->create();
        $product1 = Product::factory()->create(['price' => 45]);
        $product2 = Product::factory()->create(['price' => 60]);
        $order = Order::create([
            'total' => 165,
            'customer_id' => $customer->id,
            'status' => 'created',
        ]);

        $order->items()->createMany([
            [
                'product_id' => $product1->id,
                'name' => $product1->name,
                'price' => $product1->price,
                'quantity' => 1,
                'total' => 45,
            ],
            [
                'product_id' => $product2->id,
                'name' => $product2->name,
                'price' => $product2->price,
                'quantity' => 2,
                'total' => 120,
            ],
        ]);

        $orderService = new OrderService();
        $shoppingController = new ShoppingController();
        $showCart = $shoppingController->orderDetail($order->id, $orderService);
        expect($showCart)->toBeInstanceOf(View::class);
        expect($showCart->getData())
            ->toMatchArray([
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'total' => $order->total,
                    'customer' => [
                        'name' => $customer->name,
                        'email' => $customer->email,
                        'documentNumber' => $customer->document_number,
                    ],
                    'items' => [
                        [
                            'name' => $product1->name,
                            'price' => $product1->price,
                            'quantity' => 1,
                            'total' => $product1->price * 1,
                        ],
                        [
                            'name' => $product2->name,
                            'price' => $product2->price,
                            'quantity' => 2,
                            'total' => $product2->price * 2,
                        ],
                    ],
                ],
            ]);
    });
});
