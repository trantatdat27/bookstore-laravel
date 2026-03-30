<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CartController;

// --- Giao diện Khách hàng & Giỏ hàng ---
Route::get('/', [ClientController::class, 'index'])->name('client.home');
Route::get('/book/{id}', [ClientController::class, 'show'])->name('client.show');

Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
});

Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/place-order', [CartController::class, 'placeOrder'])->name('cart.place_order');
Route::get('/track-order', [CartController::class, 'trackOrder'])->name('cart.track');

// --- Dashboard ---
Route::get('/dashboard', function () {
    return view('dashboard'); 
})->middleware(['auth', 'verified'])->name('dashboard');

// --- Quản trị (Dùng chung AdminController) ---
Route::middleware(['auth'])->prefix('admin')->group(function () {
    
    // Quản lý Sách
    Route::get('/books', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/books', [AdminController::class, 'store'])->name('books.store');
    Route::get('/books/{id}/edit', [AdminController::class, 'edit'])->name('books.edit');
    Route::put('/books/{id}', [AdminController::class, 'update'])->name('books.update');
    Route::delete('/books/{id}', [AdminController::class, 'destroy'])->name('books.destroy');

    // Quản lý Danh mục
    Route::get('/categories', [AdminController::class, 'categoryIndex'])->name('categories.index');
    Route::post('/categories', [AdminController::class, 'categoryStore'])->name('categories.store');
    Route::delete('/categories/{id}', [AdminController::class, 'categoryDestroy'])->name('categories.destroy');

    // Quản lý Đơn hàng
    Route::get('/orders', [AdminController::class, 'orderIndex'])->name('admin.orders');
    Route::post('/orders/{id}/update', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.update');
    Route::delete('/orders/{id}', [AdminController::class, 'orderDestroy'])->name('admin.orders.destroy');
});

require __DIR__.'/auth.php';