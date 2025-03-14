<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\SuppliersController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\SupplierRequestController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/auth/register', [AuthController::class, 'register'])->name('register');

Route::group(['middleware' => ['auth:sanctum']], function () {
    /*バイヤー*/
    Route::group(['prefix' => 'requests'], function () {
        Route::get('/', [ProductsController::class, 'index'])->name('products.index');
        Route::post('/store', [ProductsController::class, 'store'])->name('products.store');
        Route::get('/detail/{product}', [ProductsController::class, 'detail'])->name('products.detail');
        Route::post('/update/{product}', [ProductsController::class, 'update'])->name('products.update');
        Route::post('/delete/{product}', [ProductsController::class, 'delete'])->name('products.delete');
        Route::post('/select_suppliers/{product}', [ProductsController::class, 'selectSuppliers'])->name('products.select_suppliers');
        Route::get('/get_selected_suppliers/{product}', [ProductsController::class, 'getSelectedSuppliers'])->name('products.get_selected_suppliers');
        Route::get('/get_quotes/{product?}', [ProductsController::class, 'getQuotes'])->name('products.quote.list');
        Route::get('/quote/detail/{quote}', [ProductsController::class, 'quoteDetail'])->name('products.quote.detail');
        Route::post('/quote/change_status/{quote}', [ProductsController::class, 'changeQuoteStatus'])->name('products.quote.change_status');
        Route::post('/quote/file_upload/{quote}', [ProductsController::class, 'fileUpload'])->name('products.quote.file_upload');
    });
    Route::group(['prefix' => 'suppliers'], function () {
        Route::get('/', [SuppliersController::class, 'index'])->name('suppliers.index');
        Route::post('/store', [SuppliersController::class, 'store'])->name('suppliers.store');
        Route::get('/detail/{supplier}', [SuppliersController::class, 'detail'])->name('suppliers.detail');
        Route::post('/update/{supplier}', [SuppliersController::class, 'update'])->name('suppliers.update');
        Route::post('/delete/{supplier}', [SuppliersController::class, 'delete'])->name('suppliers.delete');
    });
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UsersController::class, 'index'])->name('users.index');
        Route::post('/store', [UsersController::class, 'store'])->name('users.store');
        Route::get('/detail/{user}', [UsersController::class, 'detail'])->name('users.detail');
        Route::post('/update/{user}', [UsersController::class, 'update'])->name('users.update');
        Route::post('/delete/{user}', [UsersController::class, 'delete'])->name('users.delete');
    });



    /*サプライヤー*/
    Route::group(['prefix' => 'supplier_requests'], function () {
        Route::get('/', [SupplierRequestController::class, 'index'])->name('supplier_requests.index');
        Route::get('/detail/{product}', [SupplierRequestController::class, 'detail'])->name('supplier_requests.detail');
        Route::post('/update/{product}', [SupplierRequestController::class, 'update'])->name('supplier_requests.update');
        Route::post('/send_quote/{product}', [SupplierRequestController::class, 'sendQuote'])->name('supplier_requests.send_quote');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});

