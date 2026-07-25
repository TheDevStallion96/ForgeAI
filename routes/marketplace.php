<?php

use App\Http\Controllers\Marketplace\MarketplaceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
    Route::post('marketplace/plugins/{plugin}/install', [MarketplaceController::class, 'install'])->name('marketplace.install');
    Route::post('marketplace/plugins/{plugin}/uninstall', [MarketplaceController::class, 'uninstall'])->name('marketplace.uninstall');
});
