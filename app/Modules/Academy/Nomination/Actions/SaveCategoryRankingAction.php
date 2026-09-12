<?php

declare(strict_types=1);

namespace Rominas\Academy\Nomination\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Rominas\Academy\Member\Model\Member;
use Rominas\Academy\Nomination\DataTransferObjects\CategoryRankingData;
use Rominas\Academy\Nomination\Enums\NominationStatus;
use Rominas\Academy\Nomination\Model\Nomination;
use Rominas\Categories\Model\Category;

/**
 * Saves (replaces) a member's ranked picks for one category — the save/resume unit. Creates the draft
 * ballot on first save. Guards: nominations must be open, the ballot must still be a draft, the
 * category must belong to the open edition, and every nominee must exist in that category's type.
 */
class SaveCategoryRankingAction
{
    public function __construct(
        private readonly ResolveOpenNominationEditionAction $resolveOpenEdition,
    ) {}

    public function execute(Member $member, Category $category, CategoryRankingData $data): Nomination
    {
        $edition = $this->resolveOpenEdition->execute();

        if ($category->edition_id !== $edition->id) {
            throw ValidationException::withMessages([
                'category' => 'This category does not belong to the open edition.',
            ]);
        }

        $nomination = Nomination::query()->forMember($member)->forEdition($edition)->first();

        if ($nomination !== null && $nomination->status === NominationStatus::Submitted) {
            throw ValidationException::withMessages([
                'nominations' => 'Your nominations have already been submitted.',
            ]);
        }

        $this->assertNomineesAreValid($category, $data->nomineeIds);

        return DB::transaction(function () use ($member, $edition, $category, $data, $nomination): Nomination {
            $nomination ??= Nomination::create([
                'member_id' => $member->id,
                'edition_id' => $edition->id,
                'status' => NominationStatus::Draft,
            ]);

            $nomination->rankings()->where('category_id', $category->id)->delete();

            foreach ($data->nomineeIds as $index => $nomineeId) {
                $nomination->rankings()->create([
                    'category_id' => $category->id,
                    'rank' => $index + 1,
                    'nominee_type' => $category->nominee_type,
                    'nominee_id' => $nomineeId,
                ]);
            }

            return $nomination->load('rankings');
        });
    }

    /**
     * @param  list<int>  $nomineeIds
     */
    private function assertNomineesAreValid(Category $category, array $nomineeIds): void
    {
        if ($nomineeIds === []) {
            return;
        }

        $modelClass = $category->nominee_type->modelClass();
        $found = $modelClass::query()->whereIn('id', $nomineeIds)->count();

        if ($found !== count($nomineeIds)) {
            throw ValidationException::withMessages([
                'nominees' => 'One or more nominees do not exist for this category.',
            ]);
        }
    }
}
