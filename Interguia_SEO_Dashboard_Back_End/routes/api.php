<?php

use App\Http\Controllers\Api\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\SapDatabaseController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\SeoDatabaseController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// Endpoints para obtener datos de categorías, ítems/articulos, lotes y almacenes
Route::get('/v1/categories',[CategoryController::class, 'index']);
Route::get('/v1/items', [ItemController::class, 'index']);
Route::get('/v1/items/{whsCode}/warehouse', [ItemController::class, 'itemByWarehouse']);
Route::get('/v1/items/{itemCode}/batches', [ItemController::class, 'itemByBatch']);
Route::get('v1/items/{itemCode}/items', [ItemController::class, 'itemByCategory']);
Route::get('/v1/items/{itemCode}', [ItemController::class, 'itemBySerie']);
Route::get('/v1/batches', [BatchController::class, 'index']);
Route::get('/v1/warehouses', [WarehouseController::class, 'index']);


// Endpoints para configurar bases de datos SEO y SAP
Route::post('/v1/seo-database', [SeoDatabaseController::class, 'setup']);
Route::post('/v1/sap-database', [SapDatabaseController::class, 'setup']);
