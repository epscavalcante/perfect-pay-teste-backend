<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;

class OrderService
{
    public function placeOrder(array $orderData): int
    {
        $customer = Customer::create([
            'name' => $orderData['customer']['name'],
            'email' => $orderData['customer']['email'],
            'document_number' => $orderData['customer']['document_number'],
        ]);

        $orderItemsData = array_map(
            callback: function ($item) {
                $product = Product::findOrFail($item['product_id']);

                return [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'total' => $product->price * $item['quantity'],
                ];
            },
            array: $orderData['items']
        );

        $total = 0;

        foreach ($orderItemsData as $orderItemData) {
            $total += $orderItemData['total'];
        }

        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => 'created',
            'total' => $total,
        ]);

        $order->items()->createMany($orderItemsData);

        return $order->id;
    }

    public function getOrderDetail(int $orderId): array
    {
        $order = Order::with(['items', 'customer', 'lastPayment'])->findOrFail($orderId);

        $orderData = [
            'id' => $order->id,
            'status' => $order->status,
            'total' => $order->total,
            'customer' => [
                'name' => $order->customer->name,
                'email' => $order->customer->email,
                'documentNumber' => $order->customer->document_number,
            ],
            'items' => array_map(function ($item) {
                return [
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['total'],
                ];
            }, $order->items->all()),
        ];

        if ($order->lastPayment) {
            $lastPayment = [
                'paymentMethod' => $order->lastPayment->payment_method,
                'status' => $order->lastPayment->status,
                'pixQrCode' => null,
                'pixCopiaCola' => null,
                'boletoUrl' => null,
                'cardLastDigits' => null,
                'cardBrand' => null,
            ];

            if ($order->lastPayment->isPix()) {
                $lastPayment['pixQrCode'] = $order->lastPayment->metadata->pix->qr_code;
                $lastPayment['pixCopiaCola'] = $order->lastPayment->metadata->pix->copia_e_cola;
            }

            if ($order->lastPayment->isBoleto()) {
                $lastPayment['boletoUrl'] = $order->lastPayment->metadata->boleto->file_url;
            }

            if ($order->lastPayment->isCreditCard()) {
                $lastPayment['cardLastDigits'] = $order->lastPayment->metadata->credit_card->last_digits;
                $lastPayment['cardBrand'] = $order->lastPayment->metadata->credit_card->brand;
            }

            $orderData['lastPayment'] = $lastPayment;
        }

        return $orderData;
    }
}
