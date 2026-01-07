<?php

use App\Http\Controllers\api\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get('/v1/items', [ItemController::class, 'index']);
Route::get('/v1/categories',[CategoryController::class, 'index']);
Route::get('/v1/items/{batch}', [ItemController::class, 'itemByBatch']);