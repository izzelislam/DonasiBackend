<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\FundriserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'process'])->name('auth.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout'); 

Route::get('/cek-php', function(){
    return phpinfo();
});

Route::middleware('auth')->group(function(){
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');
    
    Route::resource('/donors', DonorController::class);
    Route::get('/donors/export/qr', [DonorController::class, 'printQr'])->name('donors.qr');
    Route::post('/donors/search/person', [DonorController::class, 'searchPerson'])->name('donors.search.person');
    Route::get('/donors/export/excel', [DonorController::class, 'exportExcel'])->name('donors.export.excel');
    Route::post('/donors/update/status/{id}', [DonorController::class, 'updateStatus'])->name('donors.status');
    
    Route::resource('/donations', DonationController::class);
    Route::get('/donations/recipt/{id}', [DonationController::class, 'receipt'])->name('donations.receipt');
    Route::get('/donations/print/receipt', [DonationController::class, 'printReceipt'])->name('donations.print.receipt');
    
    Route::resource('/contents', ContentController::class);

    Route::resource('/financials', FinancialController::class);
    Route::resource('/statistic', StatisticController::class)->only(['index']);
    Route::resource('/teams', TeamController::class);
    Route::resource('/users', UserController::class);
    
    Route::resource('/fundrisers', FundriserController::class);
    Route::post('/fundrisers/update/status/{id}', [FundriserController::class, 'updateStatus'])->name('fundrisers.status');
    
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
    
    Route::get('/reports-donations', [ReportController::class, 'donations'])->name('reports.donations');
    Route::get('/reports-donations/export/excel', [ReportController::class, 'exportExcel'])->name('reports.donations.export.excel');
    
    
    Route::get('/reports-financials', [ReportController::class, 'financials'])->name('reports.financials');
    Route::get('/reports-financials/export/excel', [ReportController::class, 'exportExcelFinance'])->name('reports.financials.export.excel');
});