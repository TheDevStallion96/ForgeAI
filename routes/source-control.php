<?php

use App\Http\Controllers\SourceControl\SourceControlController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('source-control', [SourceControlController::class, 'index'])->name('source-control.index');
});
