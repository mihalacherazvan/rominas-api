<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\Catalog\Album\Controllers\AlbumsController;
use Rominas\Catalog\Album\Model\Album;

Route::get('/', [AlbumsController::class, 'index'])
    ->name('index')
    ->middleware('can:viewAny,' . Album::class);

Route::post('/', [AlbumsController::class, 'create'])
    ->name('create')
    ->middleware('can:create,' . Album::class);

Route::get('/{album}', [AlbumsController::class, 'show'])
    ->name('view')
    ->middleware('can:view,album');

Route::patch('/{album}', [AlbumsController::class, 'update'])
    ->name('update')
    ->middleware('can:update,album');

Route::delete('/{album}', [AlbumsController::class, 'delete'])
    ->name('delete')
    ->middleware('can:delete,album');
