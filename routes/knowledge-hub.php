<?php

use App\Http\Controllers\KnowledgeHub\KnowledgeHubController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('knowledge', [KnowledgeHubController::class, 'index'])->name('knowledge.index');
    Route::post('knowledge', [KnowledgeHubController::class, 'store'])->name('knowledge.store');
    Route::delete('knowledge/{asset}', [KnowledgeHubController::class, 'destroy'])->name('knowledge.destroy');
});
