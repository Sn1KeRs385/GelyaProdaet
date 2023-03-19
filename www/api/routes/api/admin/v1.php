<?php

use App\Http\Controllers\Api\Admin\V1\ListOptionController;
use App\Http\Controllers\Api\Admin\V1\ProductController;
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
function addAdminRoutes(string $name, string $controller): void
{
    Route::get("/$name/all", [$controller, 'all'])->name("$name.all");
    Route::resource($name, $controller);
}

Route::middleware(['auth:sanctum'])->group(function () {
    addAdminRoutes('products', ProductController::class);
    addAdminRoutes('list-options', ListOptionController::class);
});
