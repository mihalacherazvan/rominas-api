<?php

declare(strict_types=1);

namespace Rominas\Audit\Support;

/**
 * Pseudonymizes an identifier (the attempted email on an auth route) into a non-reversible HMAC-SHA256
 * hash keyed by the stable `audit.pepper` secret, so an otherwise anonymous login attempt stays
 * correlatable without storing plaintext personal data (GDPR §6). Emails are normalized (trim +
 * lowercase) so casing/whitespace never splits one address into two hashes.
 */
final class AuditHasher
{
    public static function emailHash(string $email): string
    {
        return hash_hmac('sha256', mb_strtolower(trim($email)), (string) config('audit.pepper'));
    }
}
