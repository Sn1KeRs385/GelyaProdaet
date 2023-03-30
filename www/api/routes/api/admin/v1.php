<?php

use App\Http\Controllers\Api\Admin\V1\ListOptionController;
use App\Http\Controllers\Api\Admin\V1\ProductController;
use App\Http\Controllers\Api\Admin\V1\ProductItemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post("/product-items/{product_item}/mark-sold", [ProductItemController::class, 'markSold'])
        ->name("product-items.mark-sold");
    Route::post("/product-items/{product_item}/mark-not-for-sale", [ProductItemController::class, 'markNotForSale'])
        ->name("product-items.mark-not-for-sale");
    Route::post(
        "/product-items/{product_item}/rollback-for-sale-status",
        [ProductItemController::class, 'rollbackForSaleStatus']
    )->name("product-items.rollback-for-sale-status");

    Route::get('/product-items/all', [ProductItemController::class, 'all'])->name('product-items.all');
    Route::resource('product-items', ProductItemController::class);


    Route::get('/products/all', [ProductController::class, 'all'])->name('products.all');
    Route::resource('products', ProductController::class);

    Route::get('/list-options/all', [ListOptionController::class, 'all'])->name('list-options.all');
    Route::resource('list-options', ListOptionController::class);
});
