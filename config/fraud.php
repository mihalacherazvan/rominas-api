<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Fraud detection thresholds
    |--------------------------------------------------------------------------
    |
    | Read by the FraudMonitoring detectors (app/Modules/FraudMonitoring/Detectors).
    | A finding becomes a FraudAlert only when a cluster reaches its threshold.
    |
    */

    // N+ submitted ballots sharing one ip_hash.
    'shared_ip' => [
        'threshold' => (int) env('FRAUD_SHARED_IP_THRESHOLD', 5),
    ],

    // A spike of submissions inside a fixed-size time window.
    'velocity' => [
        'window_minutes' => (int) env('FRAUD_VELOCITY_WINDOW_MINUTES', 10),
        'threshold' => (int) env('FRAUD_VELOCITY_THRESHOLD', 50),
    ],

    // N+ ballots casting a byte-identical ordered vote set across all categories.
    'identical_ranking' => [
        'threshold' => (int) env('FRAUD_IDENTICAL_RANKING_THRESHOLD', 3),
    ],
];
