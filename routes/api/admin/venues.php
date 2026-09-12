<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\Catalog\Venue\Controllers\VenuesController;
use Rominas\Catalog\Venue\Model\Venue;

Route::get('/', [VenuesController::class, 'index'])
    ->name('index')
    ->middleware('can:viewAny,' . Venue::class);

Route::post('/', [VenuesController::class, 'create'])
    ->name('create')
    ->middleware('can:create,' . Venue::class);

Route::get('/{venue}', [VenuesController::class, 'show'])
    ->name('view')
    ->middleware('can:view,venue');

Route::patch('/{venue}', [VenuesController::class, 'update'])
    ->name('update')
    ->middleware('can:update,venue');

Route::delete('/{venue}', [VenuesController::class, 'delete'])
    ->name('delete')
    ->middleware('can:delete,venue');
