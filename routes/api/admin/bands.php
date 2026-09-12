<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\Catalog\Band\Controllers\BandsController;
use Rominas\Catalog\Band\Model\Band;

Route::get('/', [BandsController::class, 'index'])
    ->name('index')
    ->middleware('can:viewAny,' . Band::class);

Route::post('/', [BandsController::class, 'create'])
    ->name('create')
    ->middleware('can:create,' . Band::class);

Route::get('/{band}', [BandsController::class, 'show'])
    ->name('view')
    ->middleware('can:view,band');

Route::patch('/{band}', [BandsController::class, 'update'])
    ->name('update')
    ->middleware('can:update,band');

Route::delete('/{band}', [BandsController::class, 'delete'])
    ->name('delete')
    ->middleware('can:delete,band');
