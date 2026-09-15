<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\Results\Controllers\PublicResultsController;

// Public, unauthenticated results for a published edition — served from the frozen snapshot only
// (404 until the edition is published).
Route::get('/editions/{edition}', [PublicResultsController::class, 'show'])->name('editions.show');
