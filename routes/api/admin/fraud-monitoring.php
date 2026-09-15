<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\FraudMonitoring\Controllers\FraudAlertsController;
use Rominas\FraudMonitoring\Controllers\FraudMonitoringController;
use Rominas\FraudMonitoring\Model\FraudAlert;
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

// Fraud alerts: the scheduled detectors' findings, reviewable and triageable (pending/solved/dismissed).
Route::get('/{edition}/fraud-alerts', [FraudAlertsController::class, 'index'])
    ->name('fraud.alerts.index')
    ->middleware('can:viewAny,' . FraudAlert::class);

Route::get('/{edition}/fraud-alerts/{fraudAlert}', [FraudAlertsController::class, 'show'])
    ->name('fraud.alerts.show')
    ->middleware('can:view,fraudAlert');

Route::patch('/{edition}/fraud-alerts/{fraudAlert}', [FraudAlertsController::class, 'update'])
    ->name('fraud.alerts.update')
    ->middleware('can:update,fraudAlert');
