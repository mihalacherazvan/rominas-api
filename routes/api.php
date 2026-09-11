<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\Auth\Controllers\AuthController;

// Admin / management authentication (username + password → Sanctum token).
Route::post('/authenticate', [AuthController::class, 'authenticate'])->name('api.authenticate');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum')
    ->name('api.logout');

// ---------------------------------------------------------------------------
// Admin / management API (Sanctum-guarded, per-concern files under routes/api/admin/).
// ---------------------------------------------------------------------------
Route::middleware(['auth:sanctum'])->prefix('/admin')->name('api.admin.')->group(function (): void {
    Route::prefix('/permissions')->name('permissions.')->group(__DIR__ . '/api/admin/permissions.php');
    Route::prefix('/roles')->name('roles.')->group(__DIR__ . '/api/admin/roles.php');
    Route::prefix('/users')->name('users.')->group(__DIR__ . '/api/admin/users.php');
    Route::prefix('/taxonomies')->name('taxonomies.')->group(__DIR__ . '/api/admin/taxonomies.php');
    Route::prefix('/taxonomy-terms')->name('taxonomy-terms.')->group(__DIR__ . '/api/admin/taxonomy-terms.php');
});
