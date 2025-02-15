<?php

use App\Models\Product;
use App\Services\ProductService;

describe('ProductService Integration Tests', function () {
    test('Get products', function () {
        Product::factory(10)->create([
            'name' => 'Product Test',
            'price' => 150
        ]);

        $productService = new ProductService();
        $products = $productService->getProducts(2);
        expect($products)->toBeArray();
        expect($products)->toHaveCount(2);
    });
});


