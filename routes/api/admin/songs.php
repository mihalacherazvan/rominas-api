<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\Catalog\Song\Controllers\SongsController;
use Rominas\Catalog\Song\Model\Song;

Route::get('/', [SongsController::class, 'index'])
    ->name('index')
    ->middleware('can:viewAny,' . Song::class);

Route::post('/', [SongsController::class, 'create'])
    ->name('create')
    ->middleware('can:create,' . Song::class);

Route::get('/{song}', [SongsController::class, 'show'])
    ->name('view')
    ->middleware('can:view,song');

Route::patch('/{song}', [SongsController::class, 'update'])
    ->name('update')
    ->middleware('can:update,song');

Route::delete('/{song}', [SongsController::class, 'delete'])
    ->name('delete')
    ->middleware('can:delete,song');
