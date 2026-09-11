<?php

declare(strict_types=1);

use Rominas\Taxonomies\Controllers\TaxonomiesController;
use Rominas\Taxonomies\Model\Taxonomy;
use Illuminate\Support\Facades\Route;

Route::get('/', [TaxonomiesController::class, 'index'])
    ->name('index')
    ->middleware('can:viewAny,' . Taxonomy::class);

Route::post('/', [TaxonomiesController::class, 'create'])
    ->name('create')
    ->middleware('can:create,' . Taxonomy::class);

Route::get('/{taxonomy}', [TaxonomiesController::class, 'show'])
    ->name('view')
    ->middleware('can:view,taxonomy');

Route::patch('/{taxonomy}', [TaxonomiesController::class, 'update'])
    ->name('update')
    ->middleware('can:update,taxonomy');

Route::delete('/{taxonomy}', [TaxonomiesController::class, 'delete'])
    ->name('delete')
    ->middleware('can:delete,taxonomy');
