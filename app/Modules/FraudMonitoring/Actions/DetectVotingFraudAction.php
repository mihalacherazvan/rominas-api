<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Actions;

use Illuminate\Support\Facades\DB;
use Rominas\Editions\Model\Edition;
use Rominas\FraudMonitoring\DataTransferObjects\AlertCandidate;
use Rominas\FraudMonitoring\Detectors\FraudDetector;
use Rominas\FraudMonitoring\Enums\FraudAlertStatus;
use Rominas\FraudMonitoring\Model\FraudAlert;

/**
 * Runs every enabled fraud detector over an edition and records each finding as a FraudAlert. The
 * detector set is `config('fraud.enabled_detectors')`, resolved and injected by AppServiceProvider.
 * Alerts are deduped by `(edition_id, type, signature)`: a re-detected cluster updates its facts
 * (severity, context, ballot_count, membership, last_detected_at) but its `status` is never overwritten
 * — the human owns triage, so a dismissed/solved alert stays as the monitor set it even if it re-triggers.
 */
class DetectVotingFraudAction
{
    /**
     * @param  list<FraudDetector>  $detectors
     */
    public function __construct(
        private readonly array $detectors,
    ) {}

    /**
     * @return int the number of alerts created or refreshed
     */
    public function execute(Edition $edition): int
    {
        $touched = 0;

        foreach ($this->detectors as $detector) {
            foreach ($detector->detect($edition) as $candidate) {
                $this->persist($edition, $candidate);
                $touched++;
            }
        }

        return $touched;
    }

    private function persist(Edition $edition, AlertCandidate $candidate): void
    {
        DB::transaction(function () use ($edition, $candidate): void {
            // Reuse the signature's active alert (pending, or a dismissed false positive we keep
            // suppressed), but NOT a `solved` one — a fresh wave after resolution is a new episode and
            // gets its own pending alert, leaving solved rows as history.
            $alert = FraudAlert::query()
                ->where('edition_id', '=', $edition->id)
                ->where('type', '=', $candidate->type->value)
                ->where('signature', '=', $candidate->signature)
                ->where('status', '!=', FraudAlertStatus::Solved->value)
                ->first();

            if ($alert === null) {
                $alert = new FraudAlert([
                    'edition_id' => $edition->id,
                    'type' => $candidate->type,
                    'signature' => $candidate->signature,
                ]);
                $alert->status = FraudAlertStatus::Pending;
                $alert->first_detected_at = now();
            }

            $alert->severity = $candidate->severity;
            $alert->context = $candidate->context;
            $alert->ballot_count = count($candidate->ballotIds);
            $alert->last_detected_at = now();
            $alert->save();

            $alert->ballots()->sync($candidate->ballotIds);
        });
    }
}
