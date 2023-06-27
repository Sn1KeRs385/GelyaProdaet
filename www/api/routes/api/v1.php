<?php

use App\Http\Controllers\Api\V1\FileController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\SiteController;
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

Route::resource('/files', FileController::class)
    ->only(['index'])
    ->middleware(['auth:sanctum']);
Route::prefix('products')
    ->controller(ProductController::class)
    ->name('products.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
    });
Route::prefix('site')
    ->controller(SiteController::class)
    ->name('site.')
    ->group(function () {
        Route::get('/index', 'indexPage')->name('index');
    });
