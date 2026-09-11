<?php

declare(strict_types=1);

namespace Rominas\Shared\Concerns;

trait QueryBuilderSearchableTrait
{
    /**
     * @return string[]
     */
    abstract protected function getSearchableFields(): array;

    /**
     * @param array<int,string> $searchFields
     */
    public function search(?string $value = null, array $searchFields = []): self
    {
        $value = trim((string) $value);

        if ($value === '') {
            return $this;
        }

        $searchableFields = collect($this->getSearchableFields());

        if (! empty($searchFields)) {
            $searchableFields = $searchableFields->intersect($searchFields);
        }

        $this->where(function ($query) use ($searchableFields, $value): void {
            foreach ($searchableFields as $field) {
                $query->orWhere($field, 'LIKE', "%{$value}%");
            }
        });

        return $this;
    }
}
