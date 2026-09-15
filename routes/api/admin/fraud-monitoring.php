<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\FraudMonitoring\Controllers\FraudMonitoringController;
use Rominas\FraudMonitoring\Model\InvalidationBatch;

// Admin fraud monitoring, nested under an edition. The `fraudMonitoring` permission is checked against
// the InvalidationBatch class (the routes bind an Edition, not a batch row); the `fraud_monitor` and
// `custodian` roles hold it. Cancelling votes always requires a reason and is recorded as a batch.
Route::get('/{edition}/ballots', [FraudMonitoringController::class, 'ballots'])
    ->name('fraud.ballots')
    ->middleware('can:viewAny,' . InvalidationBatch::class);

Route::get('/{edition}/invalidations', [FraudMonitoringController::class, 'index'])
    ->name('fraud.invalidations.index')
    ->middleware('can:viewAny,' . InvalidationBatch::class);

Route::post('/{edition}/invalidations', [FraudMonitoringController::class, 'store'])
    ->name('fraud.invalidations.store')
    ->middleware('can:create,' . InvalidationBatch::class);

Route::get('/{edition}/invalidations/{invalidationBatch}', [FraudMonitoringController::class, 'show'])
    ->name('fraud.invalidations.show')
    ->middleware('can:view,invalidationBatch');
