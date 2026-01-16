<?php

use App\Http\Controllers\Api\FinancialRatios\IndebtednessController;
use App\Http\Controllers\Api\Inventory\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Inventory\ItemController;
use App\Http\Controllers\Api\Inventory\BatchController;
use App\Http\Controllers\Api\Inventory\UserController;
use App\Http\Controllers\SapDatabaseController;
use App\Http\Controllers\Api\Inventory\WarehouseController;
use App\Http\Controllers\SeoDatabaseController;
use App\Http\Controllers\AuthController;





// Endpoints de autenticación
Route::post('/v1/login', [AuthController::class, 'login']); // Login público

Route::middleware('auth:sanctum')->post(
    '/v1/change-password',
    [AuthController::class, 'changePassword']
);




Route::middleware('auth:sanctum')->group(function () { // Protege rutas con autenticación
    Route::post('/v1/logout', [AuthController::class, 'logout']);
    });
    

    // Endpoints para administrar usuarios y roles
    Route::middleware(['auth:sanctum', 'role:Admin,SuperAdmin'])->group(function () { // Necesitas tener uno de los roles definidos para acceder
        Route::get('/v1/users', [UserController::class, 'index']);
        Route::get('/v1/users/{user}', [UserController::class, 'show']);
        Route::put('/v1/users/{user}', [UserController::class, 'update']);
        Route::delete('/v1/users/{user}', [UserController::class, 'destroy']);

        Route::post('/v1/register', [AuthController::class, 'register']); //Necesitas permisos para crear usuarios

        

});

Route::middleware(['auth:sanctum', 'role:SuperAdmin'])->group(function () { 
    // Configurar base de datos SAP
        Route::post('/v1/sap-database', [SapDatabaseController::class, 'setup']);
});



// Endpoints para obtener datos de categorías, ítems/articulos, lotes y almacenes

Route::middleware(['auth:sanctum', 'role:Admin,SuperAdmin,Employee'])->group(function () { // Necesitas tener uno de los roles definidos para acceder

// Categories
Route::get('/v1/categories',[CategoryController::class, 'index']);

// Items
Route::get('/v1/items', [ItemController::class, 'index']);
Route::get('/v1/items/{itemCode}/batches', [ItemController::class, 'itemByBatch']); // Obtener ítems por lote
Route::get('/v1/items/{itemCode}/items', [ItemController::class, 'itemByCategory']); // Obtener ítems por categoría
Route::get('/v1/items/{whsCode}/warehouse', [ItemController::class, 'itemByWarehouse']); // Obtener ítems por almacén
Route::get('/v1/items/{itemCode}', [ItemController::class, 'itemBySerie']); // Obtener ítems por serie

// Batches y Warehouses
Route::get('/v1/batches', [BatchController::class, 'index']); // Obtener todos los lotes
Route::get('/v1/warehouses', [WarehouseController::class, 'index']); // Obtener todos los almacenes


});



// Endpoints para configurar bases de datos SEO 
Route::post('/v1/seo-database', [SeoDatabaseController::class, 'setup']); // Configurar base de datos SEO


// Endpoints para ratios financieros - Indebtedness/ endeudamiento



Route::middleware(['auth:sanctum', 'role:Admin,SuperAdmin,Employee'])->group(function () { // Necesitas tener uno de los roles definidos para acceder    

Route::post('/v1/Indebtedness/long-term-debt', [IndebtednessController::class, 'getLongTermDebtTotal']);
Route::get('/v1/Indebtedness/liabilityAccounts', [IndebtednessController::class, 'liabilityAccounts']);
Route::get(
    '/v1/Indebtedness/{date}',
    [IndebtednessController::class, 'percentageOfFinancedAssets']
);
});

