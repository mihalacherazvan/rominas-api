<?php

declare(strict_types=1);

namespace Rominas\Results\Controllers;

use Rominas\Editions\Model\Edition;
use Rominas\Results\Actions\GetEditionResultsAction;
use Rominas\Results\Model\ResultSnapshot;
use Rominas\Results\Resources\EditionResultsResource;

/**
 * Public, unauthenticated results for a published edition — served only from the frozen snapshot. An
 * edition without a snapshot (not yet published) is 404; results only ever become public at publish time.
 */
class PublicResultsController
{
    public function show(Edition $edition, GetEditionResultsAction $action): EditionResultsResource
    {
        abort_unless(
            ResultSnapshot::query()->forEdition($edition)->exists(),
            404,
        );

        return EditionResultsResource::make($action->execute($edition));
    }
}
