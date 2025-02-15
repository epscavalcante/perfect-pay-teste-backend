<?php

use App\Http\Controllers\ShoppingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShoppingController::class, 'showCart'])->name('cart');
Route::post('/', [ShoppingController::class, 'placeOrder'])->name('placeOrder');
Route::get('/orders/{order_id}', [ShoppingController::class, 'orderDetail'])->name('orderDetail');
Route::post('orders/{order_id}/process_payment', [ShoppingController::class, 'processPayment'])->name('processPayment');
