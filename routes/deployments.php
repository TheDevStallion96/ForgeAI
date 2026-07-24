<?php

use App\Http\Controllers\Deployments\DeploymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('deployments', [DeploymentController::class, 'index'])->name('deployments.index');
});
