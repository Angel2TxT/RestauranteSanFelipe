<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NotificationController;

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/shop', [App\Http\Controllers\HomeController::class, 'shop'])->name('shop');

Route::get('products/display/{product}', [ProductController::class, 'display'])->name('products.display');

Route::get('categories/display/{category}', [CategoryController::class, 'show'])->name('categories.display');

Route::get('cart/{product}', [CartController::class, 'add'])->name('cart.add');
Route::get('cart/remove/{rowId}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('order/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
Route::post('order/proccess-checkout', [OrderController::class, 'proccesCheckout'])->name('orders.proccess.checkout');

Route::get('my-orders', [OrderController::class, 'myOrders'])->name('orders.my');


Route::group(["prefix"=>"admin","middleware"=>['auth']], function(){

    Route::get('home', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.home');

    Route::resource('sliders', SliderController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('users', UserController::class);

    Route::get('orders/status/{order}', [OrderController::class, 'changeStatus'])->name('orders.status');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/report/orders', [ReportController::class, 'ordersReport'])->name('orders.report');
    Route::get('/report/users', [ReportController::class, 'usersReport'])->name('users.report');
    Route::get('/report/products', [ReportController::class, 'productsReport'])->name('products.report');
    Route::get('/report/pdf', [ReportController::class, 'generatePDF'])->name('report.pdf');
    Route::get('/reports/generate', [ReportController::class, 'generate'])->name('report.generate');
    Route::get('/reports/{type}/pdf', [ReportController::class, 'generatePDF'])->name('reports.pdf');


});


