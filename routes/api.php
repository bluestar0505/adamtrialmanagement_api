<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\SuppliersController;

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'requests'], function () {
        Route::get('/', [ProductsController::class, 'index'])->name('products.index');
        Route::post('/store', [ProductsController::class, 'store'])->name('products.store');
        Route::get('/detail/{product}', [ProductsController::class, 'detail'])->name('products.detail');
        Route::post('/update/{product}', [ProductsController::class, 'update'])->name('products.update');
        Route::post('/delete/{product}', [ProductsController::class, 'delete'])->name('products.delete');
    });

    Route::group(['prefix' => 'suppliers'], function () {
        Route::get('/', [SuppliersController::class, 'index'])->name('suppliers.index');
        Route::post('/store', [SuppliersController::class, 'store'])->name('suppliers.store');
        Route::get('/detail/{supplier}', [SuppliersController::class, 'detail'])->name('suppliers.detail');
        Route::post('/update/{supplier}', [SuppliersController::class, 'update'])->name('suppliers.update');
        Route::post('/delete/{supplier}', [SuppliersController::class, 'delete'])->name('suppliers.delete');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

