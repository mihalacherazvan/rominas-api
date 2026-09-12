<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\Academy\Nomination\Controllers\NominationsController;

// Ranked nominations for the authenticated academy member (guard `member`).
Route::get('/nominations', [NominationsController::class, 'show'])->name('nominations.show');

Route::put('/nominations/categories/{category}', [NominationsController::class, 'saveCategory'])
    ->name('nominations.categories.save');

Route::post('/nominations/submit', [NominationsController::class, 'submit'])->name('nominations.submit');
