<?php

use App\Http\Controllers\ArchitectureStudio\ArchitectureStudioController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('architecture', [ArchitectureStudioController::class, 'index'])->name('architecture.index');
    Route::get('architecture/adrs', [ArchitectureStudioController::class, 'adrs'])->name('architecture.adrs');
    Route::post('architecture/adrs', [ArchitectureStudioController::class, 'store'])->name('architecture.adrs.store');
});
