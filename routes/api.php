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
// Academy member-facing API (passwordless magic-link auth on the `member` guard).
// ---------------------------------------------------------------------------
Route::prefix('/academy')->name('api.academy.')->group(__DIR__ . '/api/academy/auth.php');

Route::middleware('auth:member')->prefix('/academy')->name('api.academy.')
    ->group(__DIR__ . '/api/academy/nominations.php');

// ---------------------------------------------------------------------------
// Admin / management API (Sanctum-guarded, per-concern files under routes/api/admin/).
// ---------------------------------------------------------------------------
Route::middleware(['auth:sanctum'])->prefix('/admin')->name('api.admin.')->group(function (): void {
    Route::prefix('/permissions')->name('permissions.')->group(__DIR__ . '/api/admin/permissions.php');
    Route::prefix('/roles')->name('roles.')->group(__DIR__ . '/api/admin/roles.php');
    Route::prefix('/users')->name('users.')->group(__DIR__ . '/api/admin/users.php');
    Route::prefix('/taxonomies')->name('taxonomies.')->group(__DIR__ . '/api/admin/taxonomies.php');
    Route::prefix('/taxonomy-terms')->name('taxonomy-terms.')->group(__DIR__ . '/api/admin/taxonomy-terms.php');

    Route::prefix('/editions')->name('editions.')->group(__DIR__ . '/api/admin/editions.php');
    Route::prefix('/categories')->name('categories.')->group(__DIR__ . '/api/admin/categories.php');
    Route::prefix('/members')->name('members.')->group(__DIR__ . '/api/admin/members.php');

    Route::prefix('/artists')->name('artists.')->group(__DIR__ . '/api/admin/artists.php');
    Route::prefix('/bands')->name('bands.')->group(__DIR__ . '/api/admin/bands.php');
    Route::prefix('/venues')->name('venues.')->group(__DIR__ . '/api/admin/venues.php');
    Route::prefix('/songs')->name('songs.')->group(__DIR__ . '/api/admin/songs.php');
    Route::prefix('/albums')->name('albums.')->group(__DIR__ . '/api/admin/albums.php');
});
