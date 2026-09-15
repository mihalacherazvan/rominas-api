<?php

declare(strict_types=1);

namespace Rominas\FraudMonitoring\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Rominas\FraudMonitoring\Enums\FraudAlertStatus;

/**
 * Sets a fraud alert's review status. Authorization is done by the route `can:` middleware.
 */
class UpdateFraudAlertStatusRequest extends FormRequest
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
            'status' => ['required', Rule::enum(FraudAlertStatus::class)],
        ];
    }

    public function status(): FraudAlertStatus
    {
        /** @var string $status */
        $status = $this->validated()['status'];

        return FraudAlertStatus::from($status);
    }
}
