<?php

declare(strict_types=1);

namespace Rominas\Categories\Actions;

use Rominas\Categories\Model\Category;

class DeleteCategoryAction
{
    public function execute(Category $category): ?bool
    {
        return $category->delete();
    }
}
