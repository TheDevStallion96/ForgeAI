<?php

use App\Http\Controllers\AI\AIChatController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('ai/chat', [AIChatController::class, 'index'])->name('ai.chat');
    Route::post('ai/chat', [AIChatController::class, 'chat']);
});
