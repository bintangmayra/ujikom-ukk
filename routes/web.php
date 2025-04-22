<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

use App\Exports\UserExport;
use App\Exports\ProductExport;
use Maatwebsite\Excel\Facades\Excel;

// Redirect root ke dashboard atau login
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Login & Logout Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Redirect ke dashboard sesuai role
Route::get('/dashboard', function () {
    return redirect()->route(auth()->user()->role . '.dashboard');
})->middleware('auth')->name('dashboard');

// Global Redirect Routes by Role
Route::middleware('auth')->group(function () {
    Route::get('/produk', fn() => redirect()->route(auth()->user()->role . '.produk.index'));
    Route::get('/user', fn() => redirect()->route(auth()->user()->role . '.user.index'));
    Route::get('/pembelian', fn() => redirect()->route(auth()->user()->role . '.pembelian.index'));
});

// ========================
// ADMIN ROUTES
// ========================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('produk', ProductController::class);
    Route::resource('user', UserController::class);

    Route::get('/pembelian', [PurchaseController::class, 'index'])->name('pembelian.index');
    Route::get('/pembelian/create', [PurchaseController::class, 'create'])->name('pembelian.create');
    Route::post('/pembelian', [PurchaseController::class, 'store'])->name('pembelian.store');
    Route::post('/pembelian/checkout', [PurchaseController::class, 'checkout'])->name('pembelian.checkout');
    Route::get('/pembelian/checkout-page', [PurchaseController::class, 'checkoutPage'])->name('pembelian.checkout.page');
    Route::get('/pembelian/export', [PurchaseController::class, 'export'])->name('pembelian.export');

    Route::post('/produk/{id}/update-stock', [ProductController::class, 'updateStock'])->name('produk.update-stock');

    Route::get('/pembelian/{purchase}/download', [PurchaseController::class, 'download'])->name('pembelian.download');
    Route::get('/pembelian/{purchase}', [PurchaseController::class, 'show'])->name('pembelian.show');

    Route::get('/admin/users/export', function() {
        return Excel::download(new UserExport, 'users.xlsx');
    })->name('user.export');
    Route::get('/admin/produk/export', function () {
        return Excel::download(new ProductExport, 'produk.xlsx');
    })->name('produk.export');
});

// ========================
// PETUGAS ROUTES

Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/produk', [ProductController::class, 'index'])->name('produk.index');
    Route::get('/produk/{id}', [ProductController::class, 'show'])->name('produk.show');

    Route::get('/pembelian', [PurchaseController::class, 'index'])->name('pembelian.index');
    Route::get('/pembelian/create', [PurchaseController::class, 'create'])->name('pembelian.create');
    Route::post('/pembelian', [PurchaseController::class, 'store'])->name('pembelian.store');
    Route::post('/pembelian/checkout', [PurchaseController::class, 'checkout'])->name('pembelian.checkout');
    Route::get('/pembelian/checkout-page', [PurchaseController::class, 'checkoutPage'])->name('pembelian.checkout.page');

    Route::post('/check-member', [MemberController::class, 'checkMember'])->name('checkMember');
    Route::get('/member', [MemberController::class, 'index'])->name('member');

    // ✅ Tambahkan ini kalau belum ada
    Route::get('/pembelian/export', [PurchaseController::class, 'export'])->name('pembelian.export');

    Route::get('/pembelian/{purchase}', [PurchaseController::class, 'show'])->name('pembelian.show');
    Route::get('/pembelian/{purchase}/download', [PurchaseController::class, 'download'])->name('pembelian.download');

    Route::get('/invoice', [MemberController::class, 'showInvoice'])->name('invoice');
});
