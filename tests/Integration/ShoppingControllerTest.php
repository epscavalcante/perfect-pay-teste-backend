<?php

use App\Http\Controllers\ShoppingController;
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
                'products' => []
            ]);
    });
});


