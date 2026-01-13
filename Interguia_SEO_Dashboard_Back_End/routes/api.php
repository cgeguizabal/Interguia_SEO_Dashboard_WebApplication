<?php

use App\Http\Controllers\Api\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\SapDatabaseController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\SeoDatabaseController;
use App\Http\Controllers\AuthController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Endpoints de autenticación
Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/v1/logout', [AuthController::class, 'logout']);
    Route::get('/v1/me', [AuthController::class, 'me']);
});

// Endpoints para administrar usuarios y roles
Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::get('/v1/users', [UserController::class, 'index']);
    Route::get('/v1/users/{user}', [UserController::class, 'show']);
    Route::put('/v1/users/{user}', [UserController::class, 'update']);
    Route::delete('/v1/users/{user}', [UserController::class, 'destroy']);
});

// Endpoints para obtener datos de categorías, ítems/articulos, lotes y almacenes

// Categories
Route::get('/v1/categories',[CategoryController::class, 'index']);

// Items
Route::get('/v1/items', [ItemController::class, 'index']);
Route::get('/v1/items/{itemCode}/batches', [ItemController::class, 'itemByBatch']);
Route::get('/v1/items/{itemCode}/items', [ItemController::class, 'itemByCategory']);
Route::get('/v1/items/{whsCode}/warehouse', [ItemController::class, 'itemByWarehouse']);
Route::get('/v1/items/{itemCode}', [ItemController::class, 'itemBySerie']);

// Batches y Warehouses
Route::get('/v1/batches', [BatchController::class, 'index']);
Route::get('/v1/warehouses', [WarehouseController::class, 'index']);


// Endpoints para configurar bases de datos SEO y SAP
Route::post('/v1/seo-database', [SeoDatabaseController::class, 'setup']);
Route::post('/v1/sap-database', [SapDatabaseController::class, 'setup']);
