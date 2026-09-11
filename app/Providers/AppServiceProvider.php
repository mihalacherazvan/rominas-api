<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->configureDeliveryServices();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Models live under `Rominas\<Module>\Model\<Name>` (not `App\Models`), so map each
        // to its flat factory `Database\Factories\<Name>Factory`. Factories set `$model`.
        Factory::guessFactoryNamesUsing(
            static fn(string $modelName): string => 'Database\\Factories\\' . class_basename($modelName) . 'Factory',
        );

        $this->configureRateLimiting();
    }

    /**
     * Wire the Delivery module's transactional-email pipeline (SMTP transport).
     */
    private function configureDeliveryServices(): void
    {
        $this->app->when(\Rominas\Delivery\Actions\DeliveryAction::class)
            ->needs('$availableDeliveryMethods')
            ->give(config('delivery.methods'));

        $this->configureSmtpMail();

        // Brevo transport — to switch, comment out configureSmtpMail() above, uncomment
        // configureBrevoMail() below, and swap the 'email' block in config/delivery.php.
        // $this->configureBrevoMail();
    }

    private function configureSmtpMail(): void
    {
        $this->app->bind(
            \Rominas\Delivery\MailServiceInterface::class,
            \Rominas\Delivery\SMTP\SmtpMailService::class,
        );

        $this->app->when(\Rominas\Delivery\SMTP\SmtpMailService::class)
            ->needs('$host')
            ->give(config('mail.mailers.smtp.host'));

        $this->app->when(\Rominas\Delivery\SMTP\SmtpMailService::class)
            ->needs('$port')
            ->give(config('mail.mailers.smtp.port'));

        $this->app->when(\Rominas\Delivery\SMTP\SmtpMailService::class)
            ->needs('$username')
            ->give(config('mail.mailers.smtp.username'));

        $this->app->when(\Rominas\Delivery\SMTP\SmtpMailService::class)
            ->needs('$password')
            ->give(config('mail.mailers.smtp.password'));

        $this->app->when(\Rominas\Delivery\SMTP\SmtpMailService::class)
            ->needs('$encryption')
            ->give(config('mail.mailers.smtp.scheme') ?? 'tls');
    }

    // Brevo transactional-email transport — requires getbrevo/brevo-php (installed) plus
    // BREVO_* credentials in config/services.php. Enable via configureDeliveryServices().
    // private function configureBrevoMail(): void
    // {
    //     $this->app->bind(
    //         \Rominas\Delivery\MailServiceInterface::class,
    //         \Rominas\Delivery\Brevo\BrevoMailService::class,
    //     );
    //
    //     $this->app->when(\Rominas\Delivery\Brevo\BrevoMailService::class)
    //         ->needs('$fromEmail')->give(config('services.brevo.from_email'));
    //     $this->app->when(\Rominas\Delivery\Brevo\BrevoMailService::class)
    //         ->needs('$fromName')->give(config('services.brevo.from_name'));
    //     $this->app->when(\Rominas\Delivery\Brevo\BrevoMailService::class)
    //         ->needs('$apiKey')->give(config('services.brevo.api_key'));
    // }

    /**
     * Throttle the passwordless magic-link / OTP endpoints per email + IP — each request
     * sends an email, so it must resist abuse.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('magic-request', static fn(Request $request): Limit => Limit::perMinute(5)
            ->by((string) $request->input('email') . '|' . $request->ip()));

        RateLimiter::for('otp-request', static fn(Request $request): Limit => Limit::perMinute(5)
            ->by((string) $request->input('email') . '|' . $request->ip()));

        RateLimiter::for('otp-login', static fn(Request $request): Limit => Limit::perMinute(10)
            ->by((string) $request->input('email') . '|' . $request->ip()));
    }
}
