<?php

use Illuminate\Support\Facades\Schedule;

// Sweep submitted public ballots for suspicious activity and record fraud alerts (FraudMonitoring).
// Requires the OS cron to run `php artisan schedule:run` every minute in production.
Schedule::command('fraud:detect')->hourly();
