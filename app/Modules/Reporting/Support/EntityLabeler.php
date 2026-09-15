<?php

declare(strict_types=1);

namespace Rominas\Reporting\Support;

use Rominas\Catalog\Enums\NomineeType;
use Rominas\Categories\Model\Category;

/**
 * Batched display-name resolution for the per-entity reports: given the category ids and the nominee ids
 * grouped by type that appear in a report's aggregate rows, it loads each set in a single query (one for
 * categories, one per Catalog type via {@see NomineeType::modelClass()}) so rendering stays free of N+1
 * queries. Mirrors the technique in Results' EditionResultsPresenter, but decoupled from Scoring DTOs.
 */
class EntityLabeler
{
    /**
     * Map per-entity aggregate entries to display rows: resolves the category and entity names in two
     * batched passes, then emits `category | entity_type | entity | <valueKey>` in the given order.
     * Deleted/unknown catalog rows fall back to an em dash.
     *
     * @param  list<array{category_id: int, nominee_type: string, nominee_id: int, value: int}>  $entries
     * @return list<array<string, string|int>>
     */
    public function labelEntityRows(array $entries, string $valueKey): array
    {
        $categoryNames = $this->categoryNames(array_map(static fn(array $e): int => $e['category_id'], $entries));

        $idsByType = [];
        foreach ($entries as $entry) {
            $idsByType[$entry['nominee_type']][] = $entry['nominee_id'];
        }
        $nomineeNames = $this->nomineeNames($idsByType);

        $rows = [];
        foreach ($entries as $entry) {
            $rows[] = [
                'category' => $categoryNames[$entry['category_id']] ?? '—',
                'entity_type' => NomineeType::from($entry['nominee_type'])->label(),
                'entity' => $nomineeNames[$entry['nominee_type']][$entry['nominee_id']] ?? '—',
                $valueKey => $entry['value'],
            ];
        }

        return $rows;
    }

    /**
     * @param  list<int>  $ids
     * @return array<int, string>  category id → name
     */
    public function categoryNames(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        /** @var array<int, string> $names */
        $names = Category::query()
            ->whereIn('id', array_values(array_unique($ids)))
            ->pluck('name', 'id')
            ->all();

        return $names;
    }

    /**
     * @param  array<string, list<int>>  $idsByType  nominee-type value → list of ids
     * @return array<string, array<int, string>>  type value → (id → name)
     */
    public function nomineeNames(array $idsByType): array
    {
        $resolved = [];

        foreach ($idsByType as $type => $ids) {
            if ($ids === []) {
                continue;
            }

            $modelClass = NomineeType::from($type)->modelClass();

            /** @var array<int, string> $names */
            $names = $modelClass::query()
                ->whereIn('id', array_values(array_unique($ids)))
                ->pluck('name', 'id')
                ->all();

            $resolved[$type] = $names;
        }

        return $resolved;
    }
}
