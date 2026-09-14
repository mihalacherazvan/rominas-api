<?php

declare(strict_types=1);

use Rominas\Catalog\Enums\NomineeType;
use Rominas\Scoring\DataTransferObjects\NomineeScore;
use Rominas\Scoring\Support\ScoreCalculator;

/**
 * Build one tally row for the calculator.
 *
 * @return array{nominee_type: NomineeType, nominee_id: int, academy_points: int, public_points: int}
 */
function tally(int $id, int $academy, int $public): array
{
    return [
        'nominee_type' => NomineeType::Artist,
        'nominee_id' => $id,
        'academy_points' => $academy,
        'public_points' => $public,
    ];
}

function calculator(): ScoreCalculator
{
    return new ScoreCalculator(precision: 6);
}

it('normalizes each class to a share and weights them 60/40', function (): void {
    // The worked example: A leads the academy, B leads the public. The 60% academy weight tips it to A.
    $scores = calculator()->rank([
        tally(id: 1, academy: 48, public: 8000),
        tally(id: 2, academy: 36, public: 12000),
        tally(id: 3, academy: 30, public: 6000),
        tally(id: 4, academy: 6, public: 4000),
    ], academyWeight: 60, publicWeight: 40);

    expect($scores)->toHaveCount(4);

    // Order A > B > C > D even though B won the public vote.
    expect(array_map(fn(NomineeScore $s): int => $s->nomineeId, $scores))->toBe([1, 2, 3, 4]);
    expect(array_map(fn(NomineeScore $s): int => $s->position, $scores))->toBe([1, 2, 3, 4]);

    // final = 0.6·academyShare + 0.4·publicShare.
    expect($scores[0]->academyShare)->toBe(0.4)          // 48/120
        ->and($scores[0]->publicShare)->toBe(0.266667)   // 8000/30000
        ->and($scores[0]->finalScore)->toBe(0.346667)
        ->and($scores[1]->finalScore)->toBe(0.34)
        ->and($scores[2]->finalScore)->toBe(0.23)
        ->and($scores[3]->finalScore)->toBe(0.083333);

    // The scores partition the category — they sum to 1.
    expect(round(array_sum(array_map(fn(NomineeScore $s): float => $s->finalScore, $scores)), 4))->toBe(1.0);
});

it('reweights the result when the class weights change', function (): void {
    $tallies = [
        tally(id: 1, academy: 48, public: 8000),   // academy leader
        tally(id: 2, academy: 36, public: 12000),  // public leader
    ];

    // At 60/40 the academy leader wins; flip the weights and the public leader takes it.
    $academyHeavy = calculator()->rank($tallies, academyWeight: 60, publicWeight: 40);
    $publicHeavy = calculator()->rank($tallies, academyWeight: 40, publicWeight: 60);

    expect($academyHeavy[0]->nomineeId)->toBe(1)
        ->and($publicHeavy[0]->nomineeId)->toBe(2);
});

it('renormalizes to academy 100% when a category has no public votes', function (): void {
    $scores = calculator()->rank([
        tally(id: 1, academy: 5, public: 0),
        tally(id: 2, academy: 4, public: 0),
    ], academyWeight: 60, publicWeight: 40);

    expect($scores[0]->nomineeId)->toBe(1)
        ->and($scores[0]->publicShare)->toBe(0.0)
        ->and($scores[0]->finalScore)->toBe(round(5 / 9, 6))   // academyShare, weight renormalized to 1.0
        ->and($scores[1]->finalScore)->toBe(round(4 / 9, 6));
});

it('lets the public decide when there are no academy points', function (): void {
    $scores = calculator()->rank([
        tally(id: 1, academy: 0, public: 3),
        tally(id: 2, academy: 0, public: 7),
    ], academyWeight: 60, publicWeight: 40);

    expect($scores[0]->nomineeId)->toBe(2)
        ->and($scores[0]->finalScore)->toBe(0.7);
});

it('breaks a dead heat in favour of the academy, then the public, then nominee id', function (): void {
    // Crafted so both nominees have an identical integer rank key (2400), but nominee 1 has more
    // academy points — the higher-weighted class wins the tie.
    $scores = calculator()->rank([
        tally(id: 1, academy: 4, public: 2),
        tally(id: 2, academy: 2, public: 6),
    ], academyWeight: 60, publicWeight: 40);

    expect($scores[0]->nomineeId)->toBe(1)
        ->and($scores[0]->finalScore)->toBe(0.5)
        ->and($scores[1]->finalScore)->toBe(0.5);
});

it('falls back to nominee id when nominees are truly identical', function (): void {
    $scores = calculator()->rank([
        tally(id: 7, academy: 5, public: 5),
        tally(id: 3, academy: 5, public: 5),
    ], academyWeight: 60, publicWeight: 40);

    expect(array_map(fn(NomineeScore $s): int => $s->nomineeId, $scores))->toBe([3, 7]);
});

it('scores a single nominee as the whole category', function (): void {
    $scores = calculator()->rank([tally(id: 1, academy: 5, public: 5)], academyWeight: 60, publicWeight: 40);

    expect($scores[0]->position)->toBe(1)
        ->and($scores[0]->academyShare)->toBe(1.0)
        ->and($scores[0]->publicShare)->toBe(1.0)
        ->and($scores[0]->finalScore)->toBe(1.0);
});

it('returns nothing for an empty category', function (): void {
    expect(calculator()->rank([], academyWeight: 60, publicWeight: 40))->toBe([]);
});
