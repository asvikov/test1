<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\CertificateController::class, 'index']);
Route::post('/certificates', [App\Http\Controllers\CertificateController::class, 'store']);
Route::post('/certificates/edit', [App\Http\Controllers\CertificateController::class, 'update']);
Route::match(['POST', 'GET'], '/payments/callback', [App\Http\Controllers\PaymentController::class, 'callback'])
    ->name('payment.callback');
Route::post('/payments/create', [App\Http\Controllers\PaymentController::class, 'create'])
    ->name('payment.create');
Route::get('/payments', [App\Http\Controllers\PaymentController::class, 'index'])->name('payment.index');
