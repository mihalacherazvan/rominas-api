<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Rominas\Editions\Model\Edition;
use Rominas\FraudMonitoring\Actions\UpdateFraudAlertStatusAction;
use Rominas\FraudMonitoring\Model\FraudAlert;
use Rominas\FraudMonitoring\QueryBuilders\FraudAlertQueryBuilder;
use Rominas\FraudMonitoring\Requests\UpdateFraudAlertStatusRequest;
use Rominas\FraudMonitoring\Resources\FraudAlertResource;
use Rominas\Users\Model\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin fraud alerts for an edition: review the scheduled detectors' findings and set each alert's triage
 * status. Authorization is the `fraudMonitoring` permission via FraudAlertPolicy.
 */
class FraudAlertsController
{
    /**
     * The edition's fraud alerts, newest activity first.
     */
    public function index(Edition $edition, Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        return FraudAlertResource::collection(
            FraudAlert::query()
                ->forEdition($edition)
                ->visibleToUser($user)
                ->orderByDesc('last_detected_at')
                ->orderByDesc('id')
                ->paginate(FraudAlertQueryBuilder::PER_PAGE),
        );
    }

    /**
     * A single alert with the ballots it implicates (hashes only).
     */
    public function show(Edition $edition, FraudAlert $fraudAlert): FraudAlertResource
    {
        abort_if($fraudAlert->edition_id !== $edition->id, Response::HTTP_NOT_FOUND);

        return FraudAlertResource::make($fraudAlert->load('ballots'));
    }

    /**
     * Set the alert's review status (pending | solved | dismissed).
     */
    public function update(
        Edition $edition,
        FraudAlert $fraudAlert,
        UpdateFraudAlertStatusRequest $request,
        UpdateFraudAlertStatusAction $action,
    ): FraudAlertResource {
        abort_if($fraudAlert->edition_id !== $edition->id, Response::HTTP_NOT_FOUND);

        return FraudAlertResource::make($action->execute($fraudAlert, $request->status()));
    }
}
