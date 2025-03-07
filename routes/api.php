<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserProductController;
use Illuminate\Support\Facades\Route;


// Route::apiResource("categories", CategoriesController::class);
Route::apiResource('users', UserController::class);
Route::post('users/{id}', [UserController::class, 'update']);
Route::apiResource('banners', BannerController::class);
Route::post('banners/{id}', [BannerController::class, 'update']);
Route::apiResource('categories', CategoryController::class);
Route::post('categories/{id}', [CategoryController::class, 'update']);
Route::apiResource('products', ProductController::class);
Route::post('products/{id}', [ProductController::class, 'update']);


Route::post("/login", [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get("/profile", [AuthController::class, 'profile']);



Route::get('/user/products', [UserProductController::class, 'index']);
Route::get('/products/{id}', [UserProductController::class, 'show']);

