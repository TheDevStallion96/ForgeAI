<?php

use App\Http\Controllers\Marketplace\MarketplaceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
});
