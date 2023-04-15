<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ModelController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployesController;
use App\Http\Controllers\BuktiBayarController;
use App\Http\Controllers\AuthenticationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//get

Route::get('/beranda', [OrderController::class, 'getOrderCounts'])->middleware(['auth:sanctum']);
Route::get('/ordersMasuk', [OrderController::class, 'showAllOrderMasuk'])->middleware(['auth:sanctum']);
Route::get('/ordersProses', [OrderController::class, 'showAllOrderProses'])->middleware(['auth:sanctum']);
Route::get('/ordersSelesai', [OrderController::class, 'showAllOrderFinish'])->middleware(['auth:sanctum']);
Route::get('/orderDetail/{id}', [OrderController::class, 'show'])->middleware(['auth:sanctum']);
Route::get('/employes', [EmployesController::class, 'index'])->middleware(['auth:sanctum']);
Route::get('/employe/{id}', [EmployesController::class, 'show'])->middleware(['auth:sanctum']);
Route::get('/orderPerMonth', [OrderController::class, 'getOrderPerMonth'])->middleware(['auth:sanctum']);
Route::get('/company', [CompanyController::class, 'index'])->middleware(['auth:sanctum']);
Route::get('/admin/{id}', [AdminController::class, 'show'])->middleware(['auth:sanctum']);
Route::get('/logout', [AuthenticationController::class, 'logout'])->middleware(['auth:sanctum']);

//post
Route::post('/login', [AuthenticationController::class, 'login']);
Route::post('/orderMasuk', [OrderController::class, 'store'])->middleware(['auth:sanctum']);
Route::post('/design', [DesignController::class, 'store'])->middleware(['auth:sanctum']);
Route::post('/model', [ModelController::class, 'store'])->middleware(['auth:sanctum']);
Route::post('/buktiBayar', [BuktiBayarController::class, 'store'])->middleware(['auth:sanctum']);
Route::post('/sendProgres', [OrderController::class, 'sendMessage'])->middleware(['auth:sanctum']);
Route::post('/employe', [EmployesController::class, 'store'])->middleware(['auth:sanctum']);


//delete
Route::delete('/order/{id}', [OrderController::class, 'destroy'])->middleware(['auth:sanctum']);
Route::delete('/design/{id}', [DesignController::class, 'destroy'])->middleware(['auth:sanctum']);
Route::delete('/model/{id}', [ModelController::class, 'destroy'])->middleware(['auth:sanctum']);
Route::delete('/buktiBayar/{id}', [BuktiBayarController::class, 'destroy'])->middleware(['auth:sanctum']);
Route::delete('/employe/{id}', [EmployesController::class, 'destroy'])->middleware(['auth:sanctum']);

//update
Route::patch('/updateOrder/{id}', [OrderController::class, 'update'])->middleware(['auth:sanctum']);
Route::patch('/updateProgres/{id}', [OrderController::class, 'updateProgres'])->middleware(['auth:sanctum']);
Route::post('/updateDownPayment/{id}', [BuktiBayarController::class, 'updateDownPayment'])->middleware(['auth:sanctum']);
Route::post('/employe/{id}', [EmployesController::class, 'update'])->middleware(['auth:sanctum']);

































































