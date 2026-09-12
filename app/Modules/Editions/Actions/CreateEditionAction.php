<?php

declare(strict_types=1);

namespace Rominas\Editions\Actions;

use Illuminate\Validation\ValidationException;
use Rominas\Editions\DataTransferObjects\EditionData;
use Rominas\Editions\Enums\EditionStatus;
use Rominas\Editions\Model\Edition;

class CreateEditionAction
{
    public function execute(EditionData $data): Edition
    {
        // Invariant: at most one non-archived edition at a time (CLAUDE.md §5).
        if (Edition::query()->active()->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Another edition is still active. Archive it before creating a new one.',
            ]);
        }

        return Edition::create([
            'name' => $data->name,
            'starts_at' => $data->starts_at,
            'nominations_start_at' => $data->nominations_start_at,
            'nominations_end_at' => $data->nominations_end_at,
            'voting_start_at' => $data->voting_start_at,
            'voting_end_at' => $data->voting_end_at,
            'ends_at' => $data->ends_at,
            'status' => EditionStatus::Draft,
        ]);
    }
}
