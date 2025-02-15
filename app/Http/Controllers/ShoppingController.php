<?php

namespace App\Http\Controllers;

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
}
