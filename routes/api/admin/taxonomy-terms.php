<?php

declare(strict_types=1);

use Rominas\Taxonomies\Controllers\TaxonomyTermsController;
use Rominas\Taxonomies\Model\TaxonomyTerm;
use Illuminate\Support\Facades\Route;

Route::get('/', [TaxonomyTermsController::class, 'index'])
    ->name('index')
    ->middleware('can:viewAny,' . TaxonomyTerm::class);

Route::post('/', [TaxonomyTermsController::class, 'create'])
    ->name('create')
    ->middleware('can:create,' . TaxonomyTerm::class);

Route::get('/{taxonomyTerm}', [TaxonomyTermsController::class, 'show'])
    ->name('view')
    ->middleware('can:view,taxonomyTerm');

Route::patch('/{taxonomyTerm}', [TaxonomyTermsController::class, 'update'])
    ->name('update')
    ->middleware('can:update,taxonomyTerm');

Route::delete('/{taxonomyTerm}', [TaxonomyTermsController::class, 'delete'])
    ->name('delete')
    ->middleware('can:delete,taxonomyTerm');
