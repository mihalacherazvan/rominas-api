<?php

declare(strict_types=1);

use Rominas\Roles\Controllers\RolesController;
use Rominas\Roles\Model\Role;
use Illuminate\Support\Facades\Route;

Route::get('/', [RolesController::class, 'index'])
    ->name('index')
    ->middleware('can:viewAny,' . Role::class);

Route::post('/', [RolesController::class, 'create'])
    ->name('create')
    ->middleware('can:create,' . Role::class);

Route::get('/{role}', [RolesController::class, 'show'])
    ->name('view')
    ->middleware('can:view,role');

Route::patch('/{role}', [RolesController::class, 'update'])
    ->name('update')
    ->middleware('can:update,role');

Route::delete('/{role}', [RolesController::class, 'delete'])
    ->name('delete')
    ->middleware('can:delete,role');
