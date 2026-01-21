<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::post('/notifications', [NotificationController::class, 'store'])
    ->name('notifications.store');
