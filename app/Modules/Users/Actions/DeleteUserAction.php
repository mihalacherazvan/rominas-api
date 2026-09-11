<?php

declare(strict_types=1);

namespace Rominas\Users\Actions;

use Rominas\Users\Model\User;

class DeleteUserAction
{
    public function execute(User $user): ?bool
    {
        return $user->delete();
    }
}
