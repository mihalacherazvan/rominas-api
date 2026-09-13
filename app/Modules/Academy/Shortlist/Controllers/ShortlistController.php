<?php

declare(strict_types=1);

namespace Rominas\Academy\Shortlist\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Rominas\Academy\Shortlist\Actions\GenerateCategoryShortlistAction;
use Rominas\Academy\Shortlist\Actions\GenerateEditionShortlistsAction;
use Rominas\Academy\Shortlist\Model\ShortlistEntry;
use Rominas\Academy\Shortlist\Resources\ShortlistEntryResource;
use Rominas\Categories\Model\Category;
use Rominas\Editions\Model\Edition;
use Rominas\Users\Model\User;

/**
 * Admin-facing nominee shortlist: review an edition's shortlist and generate it on demand (per category
 * or in bulk) once nominations close. Authorization is the `shortlists` permission via ShortlistEntryPolicy.
 */
class ShortlistController
{
    public function index(Edition $edition, Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        return ShortlistEntryResource::collection(
            ShortlistEntry::query()
                ->forEdition($edition)
                ->visibleToUser($user)
                ->with('nominee')
                ->orderBy('category_id')
                ->orderBy('position')
                ->get(),
        );
    }

    public function generateEdition(Edition $edition, GenerateEditionShortlistsAction $action): AnonymousResourceCollection
    {
        return ShortlistEntryResource::collection($action->execute($edition));
    }

    public function generateCategory(
        Edition $edition,
        Category $category,
        GenerateCategoryShortlistAction $action,
    ): AnonymousResourceCollection {
        return ShortlistEntryResource::collection($action->execute($edition, $category));
    }
}
