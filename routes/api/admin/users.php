<?php

declare(strict_types=1);

use Rominas\Users\Controllers\UsersController;
use Rominas\Users\Model\User;
use Illuminate\Support\Facades\Route;

Route::get('/', [UsersController::class, 'index'])
    ->name('index')
    ->middleware('can:viewAny,' . User::class);

Route::post('/', [UsersController::class, 'create'])
    ->name('create')
    ->middleware('can:create,' . User::class);

Route::get('/{user}', [UsersController::class, 'show'])
    ->name('view')
    ->middleware('can:view,user');

Route::patch('/{user}', [UsersController::class, 'update'])
    ->name('update')
    ->middleware('can:update,user');

Route::patch('/{user}/profile', [UsersController::class, 'updateProfile'])
    ->name('profile.update')
    ->middleware('can:updateProfile,user');

Route::delete('/{user}', [UsersController::class, 'delete'])
    ->name('delete')
    ->middleware('can:delete,user');
