<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\PaymentProcessors\FakePaymentProcessor;
use App\Services\PaymentService;

describe('PaymentService Integration Tests', function () {
    test('Receives an exception - invalid payment method', function () {

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

        $paymentProcessor = new FakePaymentProcessor();
        $paymentService = new PaymentService($paymentProcessor);

        expect(fn () => $paymentService->pay(
            orderId: $orderId,
            paymentMethod: 'unkown',
        ))->toThrow(Exception::class, 'Invalid payment method');
    });

    test('Process payment with boleto', function () {
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

        $paymentProcessor = new FakePaymentProcessor();
        $paymentService = new PaymentService($paymentProcessor);
        $paymentService->pay(
            orderId: $orderId,
            paymentMethod: 'boleto',
        );

        $order = $orderService->getOrderDetail($orderId);

        expect($order['status'])->toBe('waiting_payment');
        expect($order['lastPayment']['status'])->toBe('pending');
        expect($order['lastPayment']['paymentMethod'])->toBe('boleto');
        expect($order['lastPayment']['pixQrCode'])->toBeNull();
        expect($order['lastPayment']['pixCopiaCola'])->toBeNull();
        expect($order['lastPayment']['boletoUrl'])->toBeString();
        expect($order['lastPayment']['cardBrand'])->toBeNull();
        expect($order['lastPayment']['cardLastDigits'])->toBeNull();
    });

    test('Process payment with pix', function () {
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

        $paymentProcessor = new FakePaymentProcessor();
        $paymentService = new PaymentService($paymentProcessor);
        $paymentService->pay(
            orderId: $orderId,
            paymentMethod: 'pix',
        );

        $order = $orderService->getOrderDetail($orderId);

        expect($order['status'])->toBe('waiting_payment');
        expect($order['lastPayment']['status'])->toBe('pending');
        expect($order['lastPayment']['paymentMethod'])->toBe('pix');
        expect($order['lastPayment']['pixQrCode'])->toBeString();
        expect($order['lastPayment']['pixCopiaCola'])->toBeString();
        expect($order['lastPayment']['boletoUrl'])->toBeNull();
        expect($order['lastPayment']['cardBrand'])->toBeNull();
        expect($order['lastPayment']['cardLastDigits'])->toBeNull();
    });

    test('Process payment with credit card', function () {
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

        $paymentProcessor = new FakePaymentProcessor();
        $paymentService = new PaymentService($paymentProcessor);
        $now = now();
        $paymentService->pay(
            orderId: $orderId,
            paymentMethod: 'credit_card',
            creditCard: [
                'holder_name' => 'User Card',
                'number' => '5184019740373151',
                'expiration_date' => date('d/m', strtotime(now().'+ 3 years')),
                'cvv' => '123',
            ],
            creditCardHolder: [
                'name' => 'User Card',
                'document_number' => '55953245033',
                'email' => 'user.card@email.com',
                'phone' => '92968525161',
                'postalCode' => '70041905',
            ]
        );

        $order = $orderService->getOrderDetail($orderId);

        expect($order['status'])->toBe('paid');
        expect($order['lastPayment']['status'])->toBe('paid');
        expect($order['lastPayment']['paymentMethod'])->toBe('credit_card');
        expect($order['lastPayment']['pixQrCode'])->toBeNull();
        expect($order['lastPayment']['pixCopiaCola'])->toBeNull();
        expect($order['lastPayment']['boletoUrl'])->toBeNull();
        expect($order['lastPayment']['cardBrand'])->toBeString();
        expect($order['lastPayment']['cardLastDigits'])->toBeString();
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
