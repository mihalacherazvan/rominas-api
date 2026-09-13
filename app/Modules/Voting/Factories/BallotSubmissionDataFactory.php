<?php

declare(strict_types=1);

namespace Rominas\Voting\Factories;

use Rominas\Voting\DataTransferObjects\BallotSubmissionData;
use Rominas\Voting\DataTransferObjects\CategoryVoteData;
use Rominas\Voting\Requests\SubmitBallotRequest;

class BallotSubmissionDataFactory
{
    public static function fromRequest(SubmitBallotRequest $request): BallotSubmissionData
    {
        /** @var array{token: string, categories: list<array{category_id: int|string, nominees: list<int|string>}>} $validated */
        $validated = $request->validated();

        $categories = array_map(
            static fn(array $category): CategoryVoteData => new CategoryVoteData(
                categoryId: (int) $category['category_id'],
                nomineeIds: array_map('intval', $category['nominees']),
            ),
            $validated['categories'],
        );

        return new BallotSubmissionData(
            token: $validated['token'],
            categories: $categories,
            ip: $request->ip(),
        );
    }
}
