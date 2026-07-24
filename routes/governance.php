<?php

use App\Http\Controllers\Governance\ApiKeyController;
use App\Http\Controllers\Governance\AuditLogController;
use App\Http\Controllers\Governance\TokenBudgetController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('governance/api-keys', [ApiKeyController::class, 'index'])->name('governance.api-keys.index');
    Route::post('governance/api-keys', [ApiKeyController::class, 'store'])->name('governance.api-keys.store');
    Route::delete('governance/api-keys/{apiKey}', [ApiKeyController::class, 'destroy'])->name('governance.api-keys.destroy');

    Route::get('governance/audit-logs', [AuditLogController::class, 'index'])->name('governance.audit-logs.index');

    Route::get('governance/budget', [TokenBudgetController::class, 'show'])->name('governance.budget.show');
    Route::patch('governance/budget', [TokenBudgetController::class, 'update'])->name('governance.budget.update');
});
