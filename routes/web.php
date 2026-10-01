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
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\BlockAccessMiddleware;

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/shop', [App\Http\Controllers\HomeController::class, 'shop'])->name('shop');

Route::get('products/display/{product}', [ProductController::class, 'display'])->name('products.display');
Route::get('categories/display/{category}', [CategoryController::class, 'show'])->name('categories.display');

Route::get('cart/checkout-guest', function (\Illuminate\Http\Request $request) {
    session()->put('url.intended', route('orders.checkout'));
    if ($request->query('action') === 'register') {
        return redirect()->route('register');
    }
    return redirect()->route('login');
})->name('cart.checkout.guest');

Route::get('cart/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('cart/update/{rowId}', [CartController::class, 'update'])->name('cart.update');
Route::get('cart/remove/{rowId}', [CartController::class, 'remove'])->name('cart.remove');

Route::middleware('auth')->group(function () {
    Route::get('order/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::post('order/proccess-checkout', [OrderController::class, 'proccesCheckout'])->name('orders.proccess.checkout');
    Route::get('my-orders', [OrderController::class, 'myOrders'])->name('orders.my');

    Route::get('my-orders/{order}/edit-shop', [OrderController::class, 'startEditing'])->name('orders.edit.shop');
    Route::post('my-orders/stop-editing', [OrderController::class, 'stopEditing'])->name('orders.edit.stop');
    Route::post('my-orders/{order}/items', [OrderController::class, 'mergeCartItems'])->name('orders.items.merge');
    Route::patch('my-orders/{order}/items/{item}', [OrderController::class, 'updateItem'])->name('orders.items.update');
    Route::delete('my-orders/{order}/items/{item}', [OrderController::class, 'removeItem'])->name('orders.items.remove');
    Route::post('my-orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('my-orders/{order}/ticket', [OrderController::class, 'ticket'])->name('orders.ticket');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});

Route::prefix('admin')->middleware(['auth', BlockAccessMiddleware::class])->group(function () {
    Route::get('home', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.home');
    Route::get('kitchen', [App\Http\Controllers\KitchenController::class, 'index'])->name('kitchen.index');
    Route::get('kitchen/feed', [App\Http\Controllers\KitchenController::class, 'feed'])->name('kitchen.feed');
    Route::get('cashier', [App\Http\Controllers\CashierController::class, 'index'])->name('cashier.index');
    Route::get('cashier/feed', [App\Http\Controllers\CashierController::class, 'feed'])->name('cashier.feed');
    Route::post('cashier/orders/{order}/preview-change', [App\Http\Controllers\CashierController::class, 'previewChange'])->name('cashier.preview');
    Route::post('cashier/orders/{order}/charge', [App\Http\Controllers\CashierController::class, 'charge'])->name('cashier.charge');

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
