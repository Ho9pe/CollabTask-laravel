<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupInvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::patch('/tasks/{task}/toggle-status', [TaskController::class, 'toggleStatus'])->name('tasks.toggle-status');
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggleStatus'])->name('tasks.toggle');
    Route::resource('groups', GroupController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Group invitation routes
    Route::post('/groups/{group}/invite', [GroupInvitationController::class, 'invite'])
        ->name('groups.invite');
    Route::get('/invitations/{invitation}/accept', [GroupInvitationController::class, 'accept'])
        ->name('groups.invitations.accept');
    Route::get('/invitations/{invitation}/reject', [GroupInvitationController::class, 'reject'])
        ->name('groups.invitations.reject');
});

require __DIR__.'/auth.php';
