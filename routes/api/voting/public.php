<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\Voting\Controllers\VotingController;

// Public, accountless voting. No auth middleware — the one-time link token is the authorization.
Route::post('/request', [VotingController::class, 'request'])
    ->name('request')
    ->middleware('throttle:voting-request');
