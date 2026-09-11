<?php

declare(strict_types=1);

namespace Rominas\Users\Actions;

use Rominas\Users\DataTransferObjects\UserData;
use Rominas\Users\Model\User;

class CreateUserAction
{
    public function execute(UserData $userData): User
    {
        /** @var User $user */
        $user = User::create([
            'name' => $userData->name,
            'email' => $userData->email,
            'password' => $userData->password,
        ]);

        $user->roles()->sync($userData->associatedRoleIds);

        $user->save();

        return $user;
    }
}
