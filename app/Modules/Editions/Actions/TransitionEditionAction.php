<?php

declare(strict_types=1);

namespace Rominas\Editions\Actions;

use Illuminate\Validation\ValidationException;
use Rominas\Editions\Enums\EditionStatus;
use Rominas\Editions\Model\Edition;

class TransitionEditionAction
{
    public function execute(Edition $edition, EditionStatus $target): Edition
    {
        if (! $edition->status->canTransitionTo($target)) {
            throw ValidationException::withMessages([
                'status' => "An edition cannot move from {$edition->status->value} to {$target->value}.",
            ]);
        }

        $edition->status = $target;
        $edition->save();

        return $edition;
    }
}
