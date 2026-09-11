<?php

declare(strict_types=1);

namespace Rominas\Users\Factories;

use Rominas\Users\DataTransferObjects\ProfileData;
use Rominas\Users\Requests\UpdateProfileRequest;

class ProfileDataFactory
{
    public static function fromRequest(UpdateProfileRequest $profileRequest): ProfileData
    {
        $validatedData = $profileRequest->validated();

        return new ProfileData(
            name: $validatedData['name'],
            email: $validatedData['email'],
            // Stored via the model's `hashed` cast — pass plaintext here.
            newPassword: $validatedData['newPassword'] ?? null,
        );
    }
}
