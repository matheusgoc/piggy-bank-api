<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// profile
Route::post('profile/auth', [ProfileController::class, 'auth'])->name('profile.auth');
Route::post('profile', [ProfileController::class, 'store'])->name('profile.store');
Route::patch('profile/reset-password', [ProfileController::class, 'resetPassword'])->name('profile.reset-password');
Route::patch('profile/refresh-pin-time', [ProfileController::class, 'refreshPINTime'])->name('profile.refresh-pin-time');
Route::patch('profile/change-password-with-pin', [ProfileController::class, 'changePasswordWithPIN'])->name('profile.change-password-with-pin');

Route::middleware('auth:sanctum')->group(function () {

    // profile
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('profile/revoke', [ProfileController::class, 'revoke'])->name('profile.revoke');
    Route::get('profile/revoke-all', [ProfileController::class, 'revokeAll'])->name('profile.revoke-all');
    Route::patch('profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    // category
    Route::apiResource('categories', 'CategoryController')->except(['delete', 'destroy']);
    Route::delete('categories/{category}/{exchange?}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::get('categories/search/{search}', [CategoryController::class, 'search'])->name('categories.search');

    // transaction
    Route::apiResource('transactions', 'TransactionController')->except(['index', 'update']);
    Route::get('transactions/{year}/{month}/{limit?}', [TransactionController::class, 'list'])->name('transactions.list');
    Route::get('transactions/slice/{date?}/{direction?}/{limit?}', [TransactionController::class, 'ListSlice'])->name('transactions.list-lice');
    Route::post('transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
});

// config
Route::get('config/{key}', [ConfigController::class, 'index'])->name('config.index')->where('key', '.*');;
