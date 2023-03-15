<?php

use App\Http\Controllers\Api\AuthController;
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

Route::resource('/files', \App\Http\Controllers\Api\V1\FileController::class)
    ->only(['index'])
    ->middleware(['auth:sanctum']);

Route::resource('/albums', \App\Http\Controllers\Api\V1\AlbumController::class)
    ->only(['index', 'store'])
    ->middleware(['auth:sanctum']);
