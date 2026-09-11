<?php

declare(strict_types=1);

namespace Rominas\Users\Factories;

use Rominas\Users\DataTransferObjects\UserData;
use Rominas\Users\Requests\CreateUserRequest;
use Rominas\Users\Requests\UpdateUserRequest;

class UserDataFactory
{
    public static function fromRequest(CreateUserRequest|UpdateUserRequest $userRequest): UserData
    {
        $validatedData = $userRequest->validated();

        return new UserData(
            name: $validatedData['name'],
            email: $validatedData['email'],
            associatedRoleIds: array_map(
                static fn(array $role) => $role['id'],
                $validatedData['associatedRoles'],
            ),
            // Passwords are stored via the model's `hashed` cast — pass plaintext here.
            password: $validatedData['password'] ?? null,
            newPassword: $validatedData['newPassword'] ?? null,
        );
    }
}
