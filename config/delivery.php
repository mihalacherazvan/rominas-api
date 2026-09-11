<?php

declare(strict_types=1);

use Rominas\Delivery\SMTP\SmtpExampleNotificationPayloadFactory;
use Rominas\Delivery\SMTP\SmtpMagicLinkEmailPayloadFactory;
use Rominas\Delivery\SMTP\SmtpMailService;

return [

    /*
    |--------------------------------------------------------------------------
    | Delivery methods
    |--------------------------------------------------------------------------
    |
    | Each method maps a transport `service` to a set of payload `factories`.
    | DeliveryAction::execute($action, ['email'], ...$params) looks up
    | factories[$action], instantiates it with $params, and sends the payload
    | through `service`. Add Rominas-specific payload factories (OTP, voting
    | link, academy invitation) here as those flows land in Phase 2.
    |
    */

    'methods' => [
        'email' => [
            'service' => SmtpMailService::class,
            'factories' => [
                // action key => payload factory
                'example-notification' => SmtpExampleNotificationPayloadFactory::class,
                'magic-link-email' => SmtpMagicLinkEmailPayloadFactory::class,
            ],
        ],

        // Brevo transport — swap the 'email' block above for this and enable
        // configureBrevoMail() in AppServiceProvider. Provide Brevo payload
        // factories (extending PayloadFactoryInterface, returning BrevoMailPayload):
        //
        // 'email' => [
        //     'service' => \Rominas\Delivery\Brevo\BrevoMailService::class,
        //     'factories' => [
        //         'magic-link-email' => \Rominas\Delivery\Brevo\BrevoMagicLinkEmailPayloadFactory::class,
        //     ],
        // ],
    ],

];
