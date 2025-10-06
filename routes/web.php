<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;


Route::get('/', [PaymentController::class, 'index'])->name('home');
Route::post('/create-transaction', [PaymentController::class, 'createTransaction'])->name('create.transaction');
Route::post('/get-deposit-details', [PaymentController::class, 'getDepositDetails'])->name('get.deposit.details');
Route::post('/validate-transaction', [PaymentController::class, 'validateTransaction'])->name('validate.transaction');
