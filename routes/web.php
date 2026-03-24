<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
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

//---------------------------------------PREFIX ADMIN-----------------------------------------
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {


        // Alamat: /admin/users | Nama Route: admin.users.index
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        
        // Alamat: /admin/users/{user}/role | Nama Route: admin.users.updateRole
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');

        // Route::resource('heros', HeroController::class);

    });

require __DIR__.'/auth.php';
