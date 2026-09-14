<?php

declare(strict_types=1);

namespace Rominas\Academy\Shortlist\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Rominas\Scoring\RankPoints;

/**
 * The admin-curated final shortlist for a category: an ordered list of nominee ids (index 0 = position 1),
 * 1..RankPoints::RANKS of them, distinct. Existence and nominee-type are validated in the action against
 * the category. The category's single nominee type supplies the type — only ids are submitted.
 */
class AdjustShortlistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nominees' => 'required|array|min:1|max:' . RankPoints::RANKS,
            'nominees.*' => 'integer|distinct|min:1',
        ];
    }

    /**
     * @return list<int>
     */
    public function nomineeIds(): array
    {
        /** @var list<int|string> $nominees */
        $nominees = $this->validated()['nominees'];

        return array_map(static fn($id): int => (int) $id, $nominees);
    }
}
