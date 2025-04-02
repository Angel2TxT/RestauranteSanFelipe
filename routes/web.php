<?php

use App\Http\Controllers\ReportController;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

use App\Http\Controllers\OrderController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\ProductController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Middleware\BlockAccessMiddleware;



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
Route::patch('cart/update/{rowId}', [CartController::class, 'update'])->name('cart.update');
Route::get('cart/remove/{rowId}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('order/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
Route::post('order/proccess-checkout', [OrderController::class, 'proccesCheckout'])->name('orders.proccess.checkout');

Route::get('my-orders', [OrderController::class, 'myOrders'])->name('orders.my');


Route::group(["prefix"=>"admin","middleware"=>['auth']], function(){

    Route::get('home', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.home')->middleware(BlockAccessMiddleware::class);

    Route::resource('sliders', SliderController::class)->middleware(BlockAccessMiddleware::class);
    Route::resource('categories', CategoryController::class)->middleware(BlockAccessMiddleware::class);
    Route::get('/orders/{order}/revert', [OrderController::class, 'revertStatus'])->name('orders.revert')->middleware(BlockAccessMiddleware::class);
    Route::resource('products', ProductController::class)->middleware(BlockAccessMiddleware::class);
    Route::resource('orders', OrderController::class)->middleware(BlockAccessMiddleware::class);
    Route::resource('users', UserController::class)->middleware(BlockAccessMiddleware::class);
    Route::get('reports/users/{userId}', [ReportController::class, 'generateUserReport'])->name('reports.userReport')->middleware(BlockAccessMiddleware::class);
    Route::get('orders/{order}/report', [ReportController::class, 'generateOrderReport'])->name('orders.report')->middleware(BlockAccessMiddleware::class);
    Route::get('orders/status/{order}', [OrderController::class, 'changeStatus'])->name('orders.status')->middleware(BlockAccessMiddleware::class);
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit')->middleware(BlockAccessMiddleware::class);
    


});


