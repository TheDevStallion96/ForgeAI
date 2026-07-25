<?php

use App\Http\Controllers\Api\McpController;
use App\Http\Controllers\Api\PluginController;
use App\Http\Controllers\Api\ToolController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'throttle:api'])->prefix('v1')->group(function () {
    Route::get('/user', fn (Request $r) => $r->user());

    Route::get('/tools', [ToolController::class, 'index']);
    Route::post('/tools/{tool}/execute', [ToolController::class, 'execute']);

    Route::get('/plugins', [PluginController::class, 'index']);
    Route::post('/plugins/{plugin}/install', [PluginController::class, 'install']);
    Route::post('/plugins/{plugin}/uninstall', [PluginController::class, 'uninstall']);
    Route::get('/plugins/installed', [PluginController::class, 'installed']);

    Route::get('/mcp/connections', [McpController::class, 'index']);
    Route::post('/mcp/connections', [McpController::class, 'store']);
    Route::delete('/mcp/connections/{connection}', [McpController::class, 'destroy']);
});
