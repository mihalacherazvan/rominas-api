<?php

declare(strict_types=1);

namespace Rominas\Users\Actions;

use Rominas\Users\DataTransferObjects\UserData;
use Rominas\Users\Model\User;

class UpdateUserAction
{
    public function execute(User $user, UserData $updatedUserData): User
    {
        $user->name = $updatedUserData->name;
        $user->email = $updatedUserData->email;

        if ($updatedUserData->newPassword !== null) {
            $user->password = $updatedUserData->newPassword;
            $user->tokens()->delete();
        }

        $user->roles()->sync($updatedUserData->associatedRoleIds);

        $user->save();

        return $user;
    }
}
