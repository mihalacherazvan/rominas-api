<?php

declare(strict_types=1);

namespace Rominas\Categories\Factories;

use Rominas\Catalog\Enums\NomineeType;
use Rominas\Categories\DataTransferObjects\CategoryData;
use Rominas\Categories\Model\Category;
use Rominas\Categories\Requests\CreateCategoryRequest;
use Rominas\Categories\Requests\UpdateCategoryRequest;

class CategoryDataFactory
{
    public static function fromCreateRequest(CreateCategoryRequest $request): CategoryData
    {
        $validated = $request->validated();

        return new CategoryData(
            edition_id: (int) $validated['edition_id'],
            name: $validated['name'],
            nominee_type: NomineeType::from($validated['nominee_type']),
            position: (int) ($validated['position'] ?? 0),
            description: $validated['description'] ?? null,
        );
    }

    /**
     * On update the edition is immutable — it comes from the existing category, not the request.
     */
    public static function fromUpdateRequest(UpdateCategoryRequest $request, Category $category): CategoryData
    {
        $validated = $request->validated();

        return new CategoryData(
            edition_id: (int) $category->edition_id,
            name: $validated['name'],
            nominee_type: NomineeType::from($validated['nominee_type']),
            position: (int) ($validated['position'] ?? $category->position),
            description: $validated['description'] ?? null,
        );
    }
}
