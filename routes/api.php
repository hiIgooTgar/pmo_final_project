<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\Api\ShipmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthenticationController::class, 'register']);
    Route::post('/login', [AuthenticationController::class, 'login']);
});

Route::post('/payment/callback', [PaymentController::class, 'handleCallback']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthenticationController::class, 'logout']);

    Route::middleware('role:admin,customer,kurir')->group(function () {
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{id}', [CategoryController::class, 'show']);

        Route::get('/stock/{id}', [ProductController::class, 'checkStock']);
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{id}', [ProductController::class, 'show']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);

        Route::get('/payments', [PaymentController::class, 'index']);

        Route::post('/shipment', [ShipmentController::class, 'postShipment']);
    });

    Route::middleware('role:admin,kurir')->group(function () {
        Route::get('/shipments', [ShipmentController::class, 'index']);
        Route::put('/shipments/{id}/tracking', [ShipmentController::class, 'updateStatusLogistik']);
    });

    Route::middleware('role:customer')->group(function () {
        Route::post('/orders', [OrderController::class, 'store']);

        Route::post('/payment/request', [PaymentController::class, 'requestPayment']);
    });

    Route::middleware('role:admin,customer')->group(function () {
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
    });
});
