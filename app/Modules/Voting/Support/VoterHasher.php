<?php

declare(strict_types=1);

namespace Rominas\Voting\Support;

/**
 * Pseudonymizes voter personal data (email, IP) into non-reversible HMAC-SHA256 hashes, keyed by the
 * stable `voting.pepper` secret. The Voting module stores only these hashes — never the plaintext (GDPR).
 * The email hash is the stable identity used to enforce one ballot per person; the IP hash is captured at
 * submit for FraudMonitoring. Emails are normalized (trim + lowercase) so casing/whitespace never splits
 * one person into two identities.
 */
final class VoterHasher
{
    public static function emailHash(string $email): string
    {
        return self::hash(mb_strtolower(trim($email)));
    }

    public static function ipHash(?string $ip): ?string
    {
        if ($ip === null || $ip === '') {
            return null;
        }

        return self::hash($ip);
    }

    private static function hash(string $value): string
    {
        return hash_hmac('sha256', $value, (string) config('voting.pepper'));
    }
}
