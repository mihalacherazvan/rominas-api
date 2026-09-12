<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\Catalog\Artist\Controllers\ArtistsController;
use Rominas\Catalog\Artist\Model\Artist;

Route::get('/', [ArtistsController::class, 'index'])
    ->name('index')
    ->middleware('can:viewAny,' . Artist::class);

Route::post('/', [ArtistsController::class, 'create'])
    ->name('create')
    ->middleware('can:create,' . Artist::class);

Route::get('/{artist}', [ArtistsController::class, 'show'])
    ->name('view')
    ->middleware('can:view,artist');

Route::patch('/{artist}', [ArtistsController::class, 'update'])
    ->name('update')
    ->middleware('can:update,artist');

Route::delete('/{artist}', [ArtistsController::class, 'delete'])
    ->name('delete')
    ->middleware('can:delete,artist');
