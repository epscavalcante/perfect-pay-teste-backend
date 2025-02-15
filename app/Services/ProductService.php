<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function getProducts(int $limit): array
    {
        $products = Product::inRandomOrder()->take($limit)->get();

        return array_map(
            callback: function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                ];
            },
            array: $products->all()
        );
    }
}
