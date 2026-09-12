<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\Editions\Controllers\EditionsController;
use Rominas\Editions\Model\Edition;

Route::get('/', [EditionsController::class, 'index'])
    ->name('index')
    ->middleware('can:viewAny,' . Edition::class);

Route::post('/', [EditionsController::class, 'create'])
    ->name('create')
    ->middleware('can:create,' . Edition::class);

Route::get('/{edition}', [EditionsController::class, 'show'])
    ->name('view')
    ->middleware('can:view,edition');

Route::patch('/{edition}', [EditionsController::class, 'update'])
    ->name('update')
    ->middleware('can:update,edition');

Route::patch('/{edition}/status', [EditionsController::class, 'transition'])
    ->name('transition')
    ->middleware('can:update,edition');

Route::delete('/{edition}', [EditionsController::class, 'delete'])
    ->name('delete')
    ->middleware('can:delete,edition');
