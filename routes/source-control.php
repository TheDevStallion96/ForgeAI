<?php

use App\Http\Controllers\SourceControl\GitHubController;
use App\Http\Controllers\SourceControl\SourceControlController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('source-control', [SourceControlController::class, 'index'])->name('source-control.index');
    Route::get('source-control/{repo}', [SourceControlController::class, 'show'])->name('source-control.show');
    Route::get('source-control/{repo}/commits', [SourceControlController::class, 'commits'])->name('source-control.commits');
    Route::get('source-control/{repo}/pull-requests', [SourceControlController::class, 'pullRequests'])->name('source-control.pull-requests');
    Route::delete('source-control/{repo}', [SourceControlController::class, 'destroy'])->name('source-control.destroy');

    Route::prefix('source-control/github')->name('source-control.github.')->group(function () {
        Route::get('connect', [GitHubController::class, 'connect'])->name('connect');
        Route::post('pat', [GitHubController::class, 'storePat'])->name('pat');
        Route::get('oauth', [GitHubController::class, 'redirectToOAuth'])->name('oauth');
        Route::get('oauth/callback', [GitHubController::class, 'handleOAuthCallback'])->name('oauth.callback');
        Route::post('{installation}/disconnect', [GitHubController::class, 'disconnect'])->name('disconnect');
        Route::post('{installation}/sync', [GitHubController::class, 'syncRepos'])->name('sync');
    });
});
