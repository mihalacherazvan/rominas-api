<?php

declare(strict_types=1);

namespace Rominas\Users\Actions;

use Rominas\Users\DataTransferObjects\ProfileData;
use Rominas\Users\Model\User;

class UpdateProfileAction
{
    public function execute(User $user, ProfileData $profileData): User
    {
        $user->name = $profileData->name;
        $user->email = $profileData->email;

        if ($profileData->newPassword !== null) {
            $user->password = $profileData->newPassword;
            $user->tokens()->delete();
        }

        $user->save();

        return $user;
    }
}
