<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\Categories\Controllers\CategoriesController;
use Rominas\Categories\Model\Category;

Route::get('/', [CategoriesController::class, 'index'])
    ->name('index')
    ->middleware('can:viewAny,' . Category::class);

Route::post('/', [CategoriesController::class, 'create'])
    ->name('create')
    ->middleware('can:create,' . Category::class);

Route::get('/{category}', [CategoriesController::class, 'show'])
    ->name('view')
    ->middleware('can:view,category');

Route::patch('/{category}', [CategoriesController::class, 'update'])
    ->name('update')
    ->middleware('can:update,category');

Route::delete('/{category}', [CategoriesController::class, 'delete'])
    ->name('delete')
    ->middleware('can:delete,category');
