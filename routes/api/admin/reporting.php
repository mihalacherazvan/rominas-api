<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rominas\Reporting\Controllers\ReportsController;

// Management-facing reporting (general statistics, kept separate from final Results). All endpoints
// require the `reporting` permission (granted to admin; super_admin bypasses). A report is addressed by
// its registry key; view returns tabular JSON, export streams CSV (default) or XLSX (`?format=xlsx`).
Route::middleware('can:reporting')->group(function (): void {
    Route::get('/', [ReportsController::class, 'index'])->name('index');
    Route::get('/{report}', [ReportsController::class, 'show'])->name('show');
    Route::get('/{report}/export', [ReportsController::class, 'export'])->name('export');
});
