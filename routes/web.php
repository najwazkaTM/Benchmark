<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SourceServerController;
use App\Http\Controllers\BenchmarkController;
use App\Http\Controllers\BenchmarkResultController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('users', App\Http\Controllers\UserManagementController::class);

Route::resource('servers', SourceServerController::class);
Route::post('/servers/{server}/check', [SourceServerController::class, 'check'])->name('servers.check');
Route::post('/servers-check-all', [SourceServerController::class, 'checkAll'])->name('servers.checkAll');

Route::get('/benchmarks', [\App\Http\Controllers\BenchmarkResultController::class,'index'])->name('benchmarks.index');
Route::get('/benchmarks/create', [BenchmarkController::class,'create'])->name('benchmarks.create');
Route::post('/benchmarks/run', [BenchmarkController::class,'run'])->name('benchmarks.run');
Route::post('/benchmarks/{benchmark}/stop', [BenchmarkController::class,'stop'])->name('benchmarks.stop');
Route::get('/benchmarks/{benchmark}', [\App\Http\Controllers\BenchmarkResultController::class,'show'])->name('benchmarks.show');
Route::get('/benchmarks/{benchmark}/stream', [BenchmarkController::class,'stream'])->name('benchmarks.stream');
});

require __DIR__.'/auth.php';
