<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Services\OrderService;
use App\Services\ProductService;

class ShoppingController extends Controller
{
    public function showCart(ProductService $productService)
    {
        $products = $productService->getProducts(rand(3, 6));

        return view('cart')->with([
            'products' => $products
        ]);
    }

    public function placeOrder(PlaceOrderRequest $request, OrderService $orderService)
    {
        $customerData = [
            'name' => $request->validated('customer.name'),
            'email' => $request->validated('customer.email'),
            'document_number' => $request->validated('customer.document_number')
        ];

        $orderData = [
            'customer' => $customerData,
            'items' => array_map(function ($item) {
                return [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity']
                ];
            }, $request->validated('items'))
        ];

        $orderId = $orderService->placeOrder($orderData);

        return to_route('orderDetail', $orderId);
    }

    public function orderDetail(int $orderId, OrderService $orderService)
    {
        $orderDetail = $orderService->getOrderDetail($orderId);

        return view('order-detail')->with(['order' => $orderDetail]);
    }
}
