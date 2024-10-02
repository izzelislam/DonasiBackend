<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\DonorController;
use App\Http\Controllers\Api\ExperimenController;
use App\Http\Controllers\Api\LocaleController;
use App\Http\Controllers\Api\PermitController;
use App\Http\Controllers\Api\PresentController;
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

Route::get("/experiment", [ExperimenController::class, 'index']);

Route::middleware('auth:sanctum')->group( function() {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/update-profile', [AuthController::class, 'update']);

    Route::post('/update-password', [AuthController::class, 'updatePassword']);

    Route::get('/settings', [AuthController::class, 'settings']);

    Route::prefix('/donors')->name('donor')->group(function(){
        Route::get('/', [DonorController::class, 'index']);
        Route::get('/{id}', [DonorController::class, 'show']);
        Route::post('/', [DonorController::class, 'store']);
        Route::put('/{id}', [DonorController::class, 'update']);
        Route::delete('/{id}', [DonorController::class, 'destroy']);
        Route::get('/search', [DonorController::class, 'search']);
    });

    Route::prefix('/donations')->group(function() {
        Route::get('/', [DonationController::class, 'index']);
        Route::get('/show/{id}', [DonationController::class, 'show']);
        Route::post('/', [DonationController::class, 'store']);
        Route::put('/{id}', [DonationController::class, 'update']);
        Route::delete('/{id}', [DonationController::class, 'destroy']);
        Route::get('/search', [DonationController::class, 'search']);
        Route::get('/get-total-donation', [DonationController::class, 'getTotalDonation']);
    });

    Route::prefix('/contents')->group(function(){
        Route::get('/', [ContentController::class, 'index']);
        Route::get('/show', [ContentController::class, 'show']);
        Route::get('/search', [ContentController::class, 'search']);
    });

    Route::prefix('/presents')->group(function(){
        Route::get('/', [PresentController::class, 'index']);
        Route::post('/', [PresentController::class, 'store']);
        Route::delete('/{id}', [PresentController::class, 'destroy']);
        Route::get('/{id}', [PresentController::class, 'show']);
    });

    Route::prefix('/permits')->group(function(){
        Route::get('/', [PermitController::class, 'index']);
        Route::get('/{id}', [PermitController::class, 'show']);
        Route::post('/', [PermitController::class, 'store']);
        Route::delete('/{id}', [PermitController::class, 'destroy']);
    });

    Route::prefix('/locales')->group(function(){
        Route::get('/provinces', [LocaleController::class, 'getProvince'] );
        Route::get('/province-regencies', [LocaleController::class, 'getProvinceRegencies'] );
        Route::get('/regency-districts', [LocaleController::class, 'getRegencyDistricts'] );
        Route::get('/all', [LocaleController::class, 'getAll'] );
    });
});

