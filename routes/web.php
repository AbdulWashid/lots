<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\admin\{
                            AuthController,
                            DashboardController,
                            ProductController
                            };

Route::get('/',[AuthController::class,'index'])->name('loginform');
Route::post('/login',[AuthController::class,'login'])->name('login');
Route::get('lot/{id}',[PublicController::class,'showForm'])->name('public.form');
Route::post('lot/{id}',[PublicController::class,'handleForm'])->name('public.handleForm');

Route::group(['as' => 'admin.', 'prefix' => 'admin','middleware' => 'auth'],function(){
    Route::post('/logout',[AuthController::class,'logout'])->name('logout');
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::get('products/{product}/lots', [ProductController::class, 'fetchLots'])->name('products.fetchLots');
    Route::get('products/{product}/qr-image', [ProductController::class, 'displayQrImage'])->name('products.qrImage');
    Route::resource('lot-inquiries', LotInquiryController::class)->only(['index', 'destroy']);
});
