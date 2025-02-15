<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;

class OrderService
{
    /**
     * @return int
     */
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
                    'total' => $product->price * $item['quantity']
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
            'total' => $total
        ]);

        $order->items()->createMany($orderItemsData);

        return $order->id;
    }

    public function getOrderDetail(int $orderId): array
    {
        $order = Order::with(['items', 'customer'])->findOrFail($orderId);

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
                    'total' => $item['total']
                ];
            }, $order->items->all()),
        ];

        return $orderData;
    }
}
