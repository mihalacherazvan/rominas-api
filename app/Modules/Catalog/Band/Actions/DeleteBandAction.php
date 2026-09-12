<?php

declare(strict_types=1);

namespace Rominas\Catalog\Band\Actions;

use Rominas\Catalog\Band\Model\Band;

class DeleteBandAction
{
    public function execute(Band $band): ?bool
    {
        return $band->delete();
    }
}
