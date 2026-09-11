<?php

declare(strict_types=1);

namespace Rominas\Shared\Concerns;

trait QueryBuilderSortableTrait
{
    /**
     * @return string[]
     */
    abstract protected function getSortableFields(): array;

    /**
     * @return static
     */
    public function orderBy(?string $column = 'id', ?string $direction = 'desc'): self
    {
        if (! in_array($column, $this->getSortableFields(), true)) {
            return $this;
        }

        /**
         * @var static $builder
         */
        $builder = parent::orderBy($column, $direction);

        return $builder;
    }
}
