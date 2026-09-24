<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Middleware\BlockAccessMiddleware;

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/shop', [App\Http\Controllers\HomeController::class, 'shop'])->name('shop');

Route::get('products/display/{product}', [ProductController::class, 'display'])->name('products.display');
Route::get('categories/display/{category}', [CategoryController::class, 'show'])->name('categories.display');

Route::get('cart/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('cart/update/{rowId}', [CartController::class, 'update'])->name('cart.update');
Route::get('cart/remove/{rowId}', [CartController::class, 'remove'])->name('cart.remove');

Route::middleware('auth')->group(function () {
    Route::get('order/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::post('order/proccess-checkout', [OrderController::class, 'proccesCheckout'])->name('orders.proccess.checkout');
    Route::get('my-orders', [OrderController::class, 'myOrders'])->name('orders.my');
});

Route::prefix('admin')->middleware(['auth', BlockAccessMiddleware::class])->group(function () {
    Route::get('home', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.home');

    Route::resource('orders', OrderController::class)->except(['create', 'store', 'edit', 'update']);
    Route::resource('sliders', SliderController::class)->except(['show']);
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('users', UserController::class)->except(['show']);

    Route::post('orders/{order}/status', [OrderController::class, 'changeStatus'])->name('orders.status');
    Route::post('orders/{order}/revert', [OrderController::class, 'revertStatus'])->name('orders.revert');
    Route::get('orders/{order}/report', [ReportController::class, 'generateOrderReport'])->name('orders.report');
    Route::get('reports/users/{userId}', [ReportController::class, 'generateUserReport'])->name('reports.userReport');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
});
