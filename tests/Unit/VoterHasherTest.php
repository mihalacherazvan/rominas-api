<?php

declare(strict_types=1);

use Rominas\Voting\Support\VoterHasher;
use Tests\TestCase;

// Boot the framework (for config('voting.pepper')) without a database.
uses(TestCase::class);

it('hashes an email to a stable 64-char hex digest', function (): void {
    $hash = VoterHasher::emailHash('voter@example.test');

    expect($hash)->toBe(VoterHasher::emailHash('voter@example.test'));
    expect($hash)->toMatch('/^[0-9a-f]{64}$/');
    expect($hash)->not->toBe('voter@example.test');
});

it('normalizes casing and surrounding whitespace', function (): void {
    expect(VoterHasher::emailHash('  VOTER@Example.Test '))
        ->toBe(VoterHasher::emailHash('voter@example.test'));
});

it('hashes different emails to different digests', function (): void {
    expect(VoterHasher::emailHash('a@example.test'))
        ->not->toBe(VoterHasher::emailHash('b@example.test'));
});

it('depends on the pepper', function (): void {
    config(['voting.pepper' => 'pepper-one']);
    $first = VoterHasher::emailHash('voter@example.test');

    config(['voting.pepper' => 'pepper-two']);
    $second = VoterHasher::emailHash('voter@example.test');

    expect($first)->not->toBe($second);
});

it('hashes an IP but returns null for none', function (): void {
    expect(VoterHasher::ipHash('203.0.113.7'))->toMatch('/^[0-9a-f]{64}$/');
    expect(VoterHasher::ipHash(null))->toBeNull();
    expect(VoterHasher::ipHash(''))->toBeNull();
});
