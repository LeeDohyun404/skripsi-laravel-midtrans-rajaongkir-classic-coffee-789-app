<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController; // Pastikan ini ada

Route::post('/midtrans-callback', [OrderController::class, 'notificationHandler']);