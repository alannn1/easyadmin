<?php

use App\Http\Controllers\ChartController;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\DB;

Route::post('login', [AuthController::class, 'authenticate'])->middleware('web');
Route::get('/', [AuthController::class, 'login'])->name('login')->middleware('web');

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::get('logout', [AuthController::class, 'logout'])->name("logout");
    
    Route::resource('user', UserController::class);
    Route::get('user-api', [UserController::class, 'indexApi'])->name('user.listapi');

    Route::resource('role', RoleController::class);
    Route::get('role-api', [RoleController::class, 'indexApi'])->name('role.listapi');

    Route::get('my-account', [UserController::class, 'profile']);
    Route::post('update-profile', [UserController::class, 'updateProfile']);
    
    Route::resource('product', ProductController::class);
    Route::get('product-listapi', [ProductController::class, 'indexApi'])->name('product.listapi');
    
    Route::resource('transaction', TransactionController::class);
    Route::get('transaction-listapi', [TransactionController::class, 'indexApi'])->name('transaction.listapi');
});



