<?php

declare(strict_types=1);

namespace Rominas\Editions\Actions;

use Rominas\Editions\DataTransferObjects\EditionData;
use Rominas\Editions\Model\Edition;

class UpdateEditionAction
{
    public function execute(Edition $edition, EditionData $data): Edition
    {
        $edition->name = $data->name;
        $edition->starts_at = $data->starts_at;
        $edition->nominations_start_at = $data->nominations_start_at;
        $edition->nominations_end_at = $data->nominations_end_at;
        $edition->voting_start_at = $data->voting_start_at;
        $edition->voting_end_at = $data->voting_end_at;
        $edition->ends_at = $data->ends_at;

        $edition->save();

        return $edition;
    }
}
