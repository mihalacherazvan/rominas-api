<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\Academy\Controllers\MemberAuthController;

// Passwordless magic-link login for academy members (the `member` Sanctum guard).
Route::post('/auth/magic/request', [MemberAuthController::class, 'requestLink'])
    ->name('auth.magic.request')
    ->middleware('throttle:magic-request');

Route::post('/auth/magic/verify', [MemberAuthController::class, 'verify'])
    ->name('auth.magic.verify');

// Authenticated member endpoints.
Route::middleware('auth:member')->group(function (): void {
    Route::get('/me', [MemberAuthController::class, 'me'])->name('me');
    Route::post('/logout', [MemberAuthController::class, 'logout'])->name('logout');
});
