<?php

declare(strict_types=1);

namespace Rominas\Editions\Actions;

use Illuminate\Validation\ValidationException;
use Rominas\Editions\Enums\EditionStatus;
use Rominas\Editions\Model\Edition;

class DeleteEditionAction
{
    public function execute(Edition $edition): ?bool
    {
        // Only draft editions may be deleted; anything further along is archived instead.
        if ($edition->status !== EditionStatus::Draft) {
            throw ValidationException::withMessages([
                'status' => 'Only draft editions can be deleted.',
            ]);
        }

        return $edition->delete();
    }
}
