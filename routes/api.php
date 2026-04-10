<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;


Route::post('/send-otp',[AuthController::class,'sendOtp']);
Route::post('/verify-otp',[AuthController::class,'verifyOtp']);

Route::middleware(['auth:sanctum','role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class,'dashboard']);
    
    Route::apiResource('users',UserController::class);
    
    Route::patch('/users/{id}/toggle',[UserController::class,'toggle']);
    
    //category
    Route::apiResource('category',CategoryController::class);
    Route::patch('/category/{id}/toggle',[CategoryController::class,'toggle']);
    Route::get('/parentCategory', [CategoryController::class,'parentDropdownCategory']);

    //product
    Route::apiResource('product',ProductController::class);
    Route::patch('/product/{id}/toggle',[ProductController::class,'toggle']);
    
});

