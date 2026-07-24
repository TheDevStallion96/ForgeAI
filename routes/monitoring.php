<?php

use App\Http\Controllers\Monitoring\MonitoringController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
});
