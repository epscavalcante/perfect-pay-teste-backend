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
}
