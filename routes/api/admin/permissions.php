<?php

declare(strict_types=1);

use Rominas\Permissions\Controllers\PermissionsController;
use Rominas\Permissions\Model\Permission;
use Illuminate\Support\Facades\Route;

Route::get('/', [PermissionsController::class, 'index'])
    ->name('index')
    ->middleware('can:viewAny,' . Permission::class);

Route::post('/', [PermissionsController::class, 'create'])
    ->name('create')
    ->middleware('can:create,' . Permission::class);

Route::get('/{permission}', [PermissionsController::class, 'show'])
    ->name('view')
    ->middleware('can:view,permission');

Route::patch('/{permission}', [PermissionsController::class, 'update'])
    ->name('update')
    ->middleware('can:update,permission');

Route::delete('/{permission}', [PermissionsController::class, 'delete'])
    ->name('delete')
    ->middleware('can:delete,permission');
