<?php

use App\Http\Controllers\CollaborationController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskListController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

// Dashboard: Programmer 2 pakai DashboardController, kita tambahkan route progress terpisah
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // List & Task routes dari Programmer 2
    Route::get('/lists', [TaskListController::class, 'index'])->name('lists.index');
    Route::post('/lists', [TaskListController::class, 'store'])->name('lists.store');
    Route::get('/lists/{list}/edit', [TaskListController::class, 'edit'])->name('lists.edit');
    Route::put('/lists/{list}', [TaskListController::class, 'update'])->name('lists.update');
    Route::delete('/lists/{list}', [TaskListController::class, 'destroy'])->name('lists.destroy');
    Route::get('/lists/{list}/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/lists/{list}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Progress & Collaboration routes dari Programmer 3 (SRS-7, SRS-8)
    Route::get('/progress', [ProgressController::class, 'dashboard'])->name('progress.dashboard');
    Route::prefix('lists/{list}')->name('collaborators.')->group(function () {
        Route::get('collaborators', [CollaborationController::class, 'index'])->name('index');
        Route::post('collaborators', [CollaborationController::class, 'invite'])->name('invite');
        Route::delete('collaborators/{user}', [CollaborationController::class, 'remove'])->name('remove');
    });
});

// Admin routes dari Programmer 1
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});