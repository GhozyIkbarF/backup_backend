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

Route::middleware(['auth:sanctum'])->group(function(){
    Route::controller(OrderController::class)->group(function(){
        Route::get('/beranda','getOrderCounts');
        Route::get('/ordersMasuk','showAllOrderMasuk');
        Route::get('/ordersProses', 'showAllOrderProses');
        Route::get('/ordersSelesai', 'showAllOrderFinish');
        Route::get('/orderDetail/{id}', 'show');
        Route::get('/numbersOrderPerYear/{param}','getNumbersOrderPerYear');
        Route::post('/getOrdersPerDay','getOrdersPerDay');
        Route::post('/orderMasuk', 'store');
        Route::post('/sendProgres', 'sendMessage');
        Route::patch('/updateOrder/{id}', 'update');
        Route::patch('/updateProgres/{id}', 'updateProgres');
        Route::patch('/updateShippingCost/{id}', 'updateShippingCost');
        Route::delete('/order/{id}', 'destroy');
    });
    Route::controller(EmployesController::class)->group(function(){
        Route::get('/employes', 'index');
    });
});

Route::middleware(['auth:sanctum', 'role'])->group(function(){
    Route::controller(EmployesController::class)->group(function(){
        Route::get('/employe/{id}', 'show');
        Route::post('/employe', 'store');
        Route::delete('/employe/{id}', 'destroy');
        Route::post('/employe/{id}', 'update');
    });
    Route::controller(OrderController::class)->group(function(){
        Route::get('/getOrderReportPerDay/{startDate}/{endDate}', 'getOrderReportPerDay');
    });
});


Route::get('/orderDesign/{id}', [DesignController::class, 'getImageDesign'])->middleware(['auth:sanctum']);
Route::get('/company', [CompanyController::class, 'index'])->middleware(['auth:sanctum']);
Route::get('/admin/{id}', [AdminController::class, 'show'])->middleware(['auth:sanctum']);
Route::get('/logout', [AuthenticationController::class, 'logout'])->middleware(['auth:sanctum']);

//post
Route::post('/login', [AuthenticationController::class, 'login']);
Route::post('/orderPerMonth', [OrderController::class,'getOrderPerMonth'])->middleware(['auth:sanctum', 'role']);
Route::post('/design', [DesignController::class, 'store'])->middleware(['auth:sanctum']);
Route::post('/model', [ModelController::class, 'store'])->middleware(['auth:sanctum']);
Route::post('/buktiBayar', [BuktiBayarController::class, 'store'])->middleware(['auth:sanctum']);
Route::post('/updateDownPayment/{id}', [BuktiBayarController::class, 'updateDownPayment'])->middleware(['auth:sanctum']);

//delete

Route::delete('/design/{id}', [DesignController::class, 'destroy'])->middleware(['auth:sanctum']);
Route::delete('/model/{id}', [ModelController::class, 'destroy'])->middleware(['auth:sanctum']);
Route::delete('/buktiBayar/{id}', [BuktiBayarController::class, 'destroy'])->middleware(['auth:sanctum']);


//update
Route::patch('/company', [CompanyController::class, 'update'])->middleware(['auth:sanctum', 'role']);

//route test
Route::get('/symbolic_link', function(){
    $targetFolder = storage_path('app/public');
    $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage';
    symlink($targetFolder, $linkFolder);
});





























































