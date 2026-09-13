<?php

declare(strict_types=1);

namespace Rominas\Academy\MemberProposal\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Rominas\Academy\MemberProposal\Actions\ApproveMemberProposalAction;
use Rominas\Academy\MemberProposal\Actions\RejectMemberProposalAction;
use Rominas\Academy\MemberProposal\Enums\MemberProposalStatus;
use Rominas\Academy\MemberProposal\Model\MemberProposal;
use Rominas\Academy\MemberProposal\QueryBuilders\MemberProposalQueryBuilder;
use Rominas\Academy\MemberProposal\Requests\ReviewMemberProposalRequest;
use Rominas\Academy\MemberProposal\Resources\MemberProposalResource;
use Rominas\Users\Model\User;

/**
 * Admin-side review of member proposals (guard `sanctum` + the `memberProposals` permission).
 */
class AdminMemberProposalsController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        $query = MemberProposal::query()->visibleToUser($user);

        if ($request->filled('status')) {
            $query = $query->filterByStatus(MemberProposalStatus::from((string) $request->query('status')));
        }

        return MemberProposalResource::collection(
            $query
                ->search($request->query('search'))
                ->orderBy($request->query('orderBy', 'created_at'), $request->query('orderDir', 'desc'))
                ->paginate($request->query('perPage', MemberProposalQueryBuilder::PER_PAGE)),
        );
    }

    public function show(MemberProposal $memberProposal): MemberProposalResource
    {
        return new MemberProposalResource($memberProposal);
    }

    public function approve(
        MemberProposal $memberProposal,
        ReviewMemberProposalRequest $request,
        ApproveMemberProposalAction $action,
    ): MemberProposalResource {
        /** @var User $user */
        $user = $request->user();

        $proposal = $action->execute($memberProposal, $user, $request->validated()['note'] ?? null);

        return new MemberProposalResource($proposal);
    }

    public function reject(
        MemberProposal $memberProposal,
        ReviewMemberProposalRequest $request,
        RejectMemberProposalAction $action,
    ): MemberProposalResource {
        /** @var User $user */
        $user = $request->user();

        $proposal = $action->execute($memberProposal, $user, $request->validated()['note'] ?? null);

        return new MemberProposalResource($proposal);
    }
}
