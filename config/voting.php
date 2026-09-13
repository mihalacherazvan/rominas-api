<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Voter pseudonymization pepper
    |--------------------------------------------------------------------------
    |
    | Stable secret keying the HMAC hashes of voter identifiers (email_hash,
    | ip_hash) stored by the Voting module. Personal data is never persisted in
    | plaintext (GDPR); the pepper makes the hashes non-reversible without it.
    |
    | It MUST be set and MUST NOT be rotated once ballots exist — rotating it
    | breaks the one-link-per-email lookup (every existing email_hash would stop
    | matching). Falls back to APP_KEY so non-production works out of the box;
    | set a dedicated, stable VOTING_PEPPER in production.
    |
    */

    'pepper' => env('VOTING_PEPPER', env('APP_KEY', '')),

];
