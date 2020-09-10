<?php

use App\Http\Controllers\ProfileController;
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

Route::post('/profile/auth', [ProfileController::class, 'auth'])->name('profile.auth');
Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/revoke', [ProfileController::class, 'revoke'])->name('profile.revoke');
    Route::get('/profile/revoke-all', [ProfileController::class, 'revokeAll'])->name('profile.revoke-all');
});
