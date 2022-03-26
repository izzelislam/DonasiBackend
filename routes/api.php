<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\DonorController;
use App\Http\Controllers\Api\LocaleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group( function() {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/update-profile', [AuthController::class, 'update']);

    Route::get('/settings', [AuthController::class, 'settings']);

    Route::prefix('/donors')->name('donor')->group(function(){
        Route::get('/', [DonorController::class, 'index']);
        Route::get('/show', [DonorController::class, 'show']);
        Route::post('/store', [DonorController::class, 'store']);
        Route::put('/update', [DonorController::class, 'update']);
        Route::delete('/delete', [DonorController::class, 'destroy']);
        Route::get('/search', [DonorController::class, 'search']);
    });

    Route::prefix('/donations')->group(function() {
        Route::get('/', [DonationController::class, 'index']);
        Route::get('/show', [DonationController::class, 'show']);
        Route::post('/store', [DonationController::class, 'store']);
        Route::put('/update', [DonationController::class, 'update']);
        Route::delete('/delete', [DonationController::class, 'destroy']);
        Route::get('/search', [DonationController::class, 'search']);
        Route::get('/get-total-donation', [DonationController::class, 'getTotalDonation']);
    });

    Route::prefix('/contents')->group(function(){
        Route::get('/', [ContentController::class, 'index']);
        Route::get('/show', [ContentController::class, 'show']);
        Route::get('/search', [ContentController::class, 'search']);
    });

    Route::prefix('/locales')->group(function(){
        Route::get('/provinces', [LocaleController::class, 'getProvince'] );
        Route::get('/province-regencies', [LocaleController::class, 'getProvinceRegencies'] );
        Route::get('/regency-districts', [LocaleController::class, 'getRegencyDistricts'] );
        Route::get('/all', [LocaleController::class, 'getAll'] );
    });
});

