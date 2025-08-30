<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\admin\{
                            AuthController,
                            DashboardController,
                            ProductController,
                            LotInquiryController
                            };
Route::get('/', function () {
    return redirect()->route('loginform');
});
Route::get('/login',[AuthController::class,'index'])->name('loginform');
Route::post('/login',[AuthController::class,'login'])->name('login');

Route::get('lot/{id}',[PublicController::class,'showForm'])->name('public.form');
Route::post('lotDetail/{id}',[PublicController::class,'handleForm'])->name('public.handleForm');

Route::group(['as' => 'admin.', 'prefix' => 'admin','middleware' => 'auth'],function(){

    Route::post('/logout',[AuthController::class,'logout'])->name('logout');

    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');

    Route::resource('products', ProductController::class);
    Route::get('products/{product}/lots', [ProductController::class, 'fetchLots'])->name('products.fetchLots');// 1
    // Route::get('products/{product}/qr-image', [ProductController::class, 'displayQrImage'])->name('products.qrImage');

    Route::get('lots/{lot}/qr-image', [ProductController::class, 'displayQrImage'])->name('products.qrImage');
    Route::get('lots/{lot}/qr-download', [ProductController::class, 'downloadQr'])->name('products.qrDownload');

    Route::get('/inquiries/export', [LotInquiryController::class, 'export'])->name('inquiries.export');
    Route::resource('lot-inquiries', LotInquiryController::class)->only(['index', 'destroy']);

});
