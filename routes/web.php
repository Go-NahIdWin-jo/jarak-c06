<?php

use App\Http\Controllers\CollaborationController;
use App\Http\Controllers\ProgressController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [ProgressController::class, 'dashboard'])->name('dashboard');

Route::prefix('lists/{list}')->name('collaborators.')->group(function () {
    Route::get('collaborators', [CollaborationController::class, 'index'])->name('index');
    Route::post('collaborators', [CollaborationController::class, 'invite'])->name('invite');
    Route::delete('collaborators/{user}', [CollaborationController::class, 'remove'])->name('remove');
});
