<?php

use App\Http\Controllers\api\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get('/v1/categories',[CategoryController::class, 'index']);
Route::get('/v1/items', [ItemController::class, 'index']);
Route::get('/v1/items/{whsCode}/warehouse', [ItemController::class, 'itemByWarehouse']);
Route::get('/v1/items/{itemCode}/batches', [ItemController::class, 'itemByBatch']);
Route::get('v1/items/{itemCode}/items', [ItemController::class, 'itemByCategory']);
Route::get('/v1/items/{itemCode}', [ItemController::class, 'itemBySerie']);
