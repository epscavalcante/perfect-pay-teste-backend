<?php

use App\Http\Controllers\ShoppingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShoppingController::class, 'showCart'])->name('cart');
