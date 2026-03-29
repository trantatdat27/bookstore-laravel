<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CartController;

// --- Giao diện Khách hàng ---
Route::get('/', [ClientController::class, 'index'])->name('client.home');
Route::get('/book/{id}', [ClientController::class, 'show'])->name('client.show');

// --- Giỏ hàng ---
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
});

// --- Thanh toán & Tra cứu ---
Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/place-order', [CartController::class, 'placeOrder'])->name('cart.place_order');
Route::get('/track-order', [CartController::class, 'trackOrder'])->name('cart.track');

// --- Trang Dashboard (Mặc định của Breeze sau khi đăng nhập) ---
Route::get('/dashboard', function () {
    return view('dashboard'); 
})->middleware(['auth', 'verified'])->name('dashboard');

// --- Giao diện Quản trị (ĐÃ BẢO MẬT: Phải đăng nhập mới được vào) ---
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/books', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/books', [AdminController::class, 'store'])->name('books.store');
    Route::get('/books/{id}/edit', [AdminController::class, 'edit'])->name('books.edit');
    Route::put('/books/{id}', [AdminController::class, 'update'])->name('books.update');
    Route::delete('/books/{id}', [AdminController::class, 'destroy'])->name('books.destroy');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

    // Quản lý đơn hàng
    Route::get('/orders', [AdminController::class, 'orderIndex'])->name('admin.orders');
    Route::post('/orders/{id}/update', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.update');
});

// --- Nhúng các Route Đăng nhập/Đăng ký của Laravel Breeze ---
require __DIR__.'/auth.php';