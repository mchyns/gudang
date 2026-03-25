<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Superadmin\InsightController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ----------------------------------------------------------------------------------
// ADMIN & SUPERADMIN ROUTES
// ----------------------------------------------------------------------------------
Route::middleware(['auth', 'role:admin,superadmin'])->prefix('admin')->name('admin.')->group(function () {
    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');

    // Partners (Supplier & Dapur)
    Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');
    Route::get('/partners/{user}', [PartnerController::class, 'show'])->name('partners.show');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::patch('/orders/{order}', [OrderController::class, 'updateStatus'])->name('orders.update-status');

    Route::middleware('role:admin')->group(function () {
        Route::get('/orders/{order}/operational', [OrderController::class, 'editOperational'])->name('orders.operational.edit');
        Route::patch('/orders/{order}/operational', [OrderController::class, 'updateOperational'])->name('orders.operational.update');
    });

    // Finance & Salary Distribution
    Route::middleware('role:admin')->group(function () {
        Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::post('/finance/distribution', [FinanceController::class, 'storeDistribution'])->name('finance.distribution.store');
    });

    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    
    // Manage Products (Admin can edit selling price)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
});

// ----------------------------------------------------------------------------------
// SUPPLIER ROUTES
// ----------------------------------------------------------------------------------
Route::middleware(['auth', 'role:supplier'])->prefix('supplier')->name('supplier.')->group(function () {
    // Supplier manages their own products
    Route::resource('products', ProductController::class)->except(['show']);
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
});

// ----------------------------------------------------------------------------------
// DAPUR ROUTES
// ----------------------------------------------------------------------------------
Route::middleware(['auth', 'role:dapur'])->prefix('dapur')->name('dapur.')->group(function () {
    Route::get('/orders/new', [OrderController::class, 'create'])->name('orders.create'); // New Order Form
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store'); // Create order (Checkout)
    Route::get('/orders', [OrderController::class, 'dapurIndex'])->name('orders.my_orders'); // View order history (changed name slightly to avoid conflict if any, but index is standard)
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
});

Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/insights', [InsightController::class, 'index'])->name('insights.index');
    Route::get('/insights/print/{period}', [InsightController::class, 'printPeriod'])
        ->whereIn('period', ['daily', 'weekly', 'monthly'])
        ->name('insights.print');
});

require __DIR__.'/auth.php';
