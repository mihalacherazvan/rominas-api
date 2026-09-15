<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sweep submitted public ballots for suspicious activity and record fraud alerts (FraudMonitoring).
// Requires the OS cron to run `php artisan schedule:run` every minute in production.
Schedule::command('fraud:detect')->hourly();
