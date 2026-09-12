<?php

declare(strict_types=1);

namespace Rominas\Auth\MagicLink\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Rominas\Auth\MagicLink\Model\MagicLinkToken;
use Rominas\Delivery\Actions\DeliveryAction;

/**
 * Issues a passwordless magic link for an email address on a given participant guard. The token is
 * stored (hashed) in `magic_link_tokens`, keyed by (email, guard). Guard-agnostic: the authenticatable
 * is resolved through the guard's own auth provider (config/auth.php), so the same action serves any
 * participant guard.
 *
 * The system is CLOSED — a link is only issued for an address that already has an account (an admin
 * invites members first). When no account matches, we return silently so the caller can always
 * respond 200 without leaking whether the address exists. Within the resend cooldown a still-valid
 * link is kept and nothing is re-sent.
 */
class SendMagicLinkAction
{
    private const int TTL_MINUTES = 15;

    private const int RESEND_COOLDOWN_SECONDS = 60;

    public function __construct(
        private readonly DeliveryAction $delivery,
    ) {}

    public function execute(string $guard, string $email, string $emailAction): void
    {
        $provider = config("auth.guards.{$guard}.provider");

        if (! is_string($provider)) {
            return;
        }

        // Closed system: only issue a link for an address that already has an account.
        if (Auth::createUserProvider($provider)?->retrieveByCredentials(['email' => $email]) === null) {
            return;
        }

        $existing = MagicLinkToken::query()
            ->where('email', $email)
            ->where('guard', $guard)
            ->first();

        if ($existing !== null
            && $existing->created_at?->gt(now()->subSeconds(self::RESEND_COOLDOWN_SECONDS))
            && $existing->created_at->gt(now()->subMinutes(self::TTL_MINUTES))) {
            return;
        }

        $token = Str::random(48);

        MagicLinkToken::query()->updateOrCreate(
            ['email' => $email, 'guard' => $guard],
            ['token' => Hash::make($token), 'created_at' => now()],
        );

        $this->delivery->execute($emailAction, ['email'], $email, $token);
    }
}
