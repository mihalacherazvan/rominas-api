<?php

declare(strict_types=1);

namespace Rominas\Academy\MemberProposal\DataTransferObjects;

class MemberProposalData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $reason = null,
    ) {}
}
