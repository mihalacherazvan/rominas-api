<?php

declare(strict_types=1);

namespace Rominas\Voting\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;
use Rominas\Editions\Model\Edition;
use Rominas\Voting\Enums\BallotStatus;
use Rominas\Voting\Model\Ballot;

/**
 * @extends Builder<Ballot>
 */
class BallotQueryBuilder extends Builder
{
    public function forEdition(Edition $edition): self
    {
        return $this->where('edition_id', '=', $edition->id);
    }

    public function byEmailHash(string $emailHash): self
    {
        return $this->where('email_hash', '=', $emailHash);
    }

    public function byTokenHash(string $tokenHash): self
    {
        return $this->where('token_hash', '=', $tokenHash);
    }

    public function issued(): self
    {
        return $this->where('status', '=', BallotStatus::Issued->value);
    }

    public function submitted(): self
    {
        return $this->where('status', '=', BallotStatus::Submitted->value);
    }
}
