<?php

use App\Http\Controllers\Agents\AgentController;
use App\Http\Controllers\Agents\ExecutionSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('agents', [AgentController::class, 'index'])->name('agents.index');
    Route::get('agents/create', [AgentController::class, 'create'])->name('agents.create');
    Route::post('agents', [AgentController::class, 'store'])->name('agents.store');
    Route::get('agents/{agent}/edit', [AgentController::class, 'edit'])->name('agents.edit');
    Route::patch('agents/{agent}', [AgentController::class, 'update'])->name('agents.update');
    Route::delete('agents/{agent}', [AgentController::class, 'destroy'])->name('agents.destroy');

    Route::get('agents/sessions', [ExecutionSessionController::class, 'index'])->name('agents.sessions.index');
    Route::get('agents/{agent}/sessions', [ExecutionSessionController::class, 'index'])->name('agents.sessions.by-agent');
    Route::get('agents/sessions/{session}', [ExecutionSessionController::class, 'show'])->name('agents.sessions.show');
    Route::delete('agents/sessions/{session}', [ExecutionSessionController::class, 'destroy'])->name('agents.sessions.destroy');
});
