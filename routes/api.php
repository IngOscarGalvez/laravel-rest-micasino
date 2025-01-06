<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::post('/easy-money', [PaymentController::class, 'processEasyMoney']);

// Ruta para SuperWalletz
Route::post('/super-walletz', [PaymentController::class, 'processSuperWalletz']);

// Webhook para SuperWalletz
Route::post('/webhook/superwalletz', [PaymentController::class, 'handleSuperWalletzWebhook'])
    ->name('webhook.superwalletz');
