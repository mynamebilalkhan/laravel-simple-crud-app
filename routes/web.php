<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(ProductController::class)->group(function () {

    Route::get('/products', 'index')->name('products.index');
    Route::get('/products/create', 'create')->name('products.create');
    Route::post('/products', 'store')->name('products.store');
    Route::get('/product/{product}/edit', 'edit')->name('products.edit');
    Route::put('/product/{product}', 'update')->name('products.update');
    Route::delete('/product/{product}', 'destroy')->name('products.destroy');
});
