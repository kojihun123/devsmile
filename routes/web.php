<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
// use App\Http\Controllers\OrderController;
// use App\Http\Controllers\PaymentController;
// use App\Http\Controllers\Admin\DashboardController;
// use App\Http\Controllers\Admin\ProductController as AdminProductController;
// use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use Illuminate\Support\Facades\Route;

// 홈 + 상품 목록
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
// Route::get('/categories/{category}', [ProductController::class, 'category'])->name('categories.show');

// 장바구니 (비회원 + 회원)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');

// 주문 (비회원 + 회원)
// Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
// Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

// 결제 콜백 (토스페이먼츠)
// Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
// Route::get('/payment/fail', [PaymentController::class, 'fail'])->name('payment.fail');

// 회원 전용
// Route::middleware('auth')->group(function () {
//     Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
//     Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
// });

// 관리자
// Route::prefix('admin')
//     ->middleware(['auth', 'admin'])
//     ->name('admin.')
//     ->group(function () {
//         Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
//         Route::resource('products', AdminProductController::class);
//         Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'update']);
//     });

require __DIR__.'/auth.php';
