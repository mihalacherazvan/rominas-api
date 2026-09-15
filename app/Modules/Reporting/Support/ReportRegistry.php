<?php

declare(strict_types=1);

namespace Rominas\Reporting\Support;

use Illuminate\Contracts\Container\Container;
use Rominas\Reporting\Reports\ReportInterface;

/**
 * The catalogue of available reports. Report classes are listed in `config/reporting.php` (`reports`);
 * this registry instantiates them through the container and indexes them by their `key()`, so the HTTP
 * layer can list every report or resolve one by the key in the URL.
 */
class ReportRegistry
{
    /**
     * @var array<string, ReportInterface>|null
     */
    private ?array $reports = null;

    public function __construct(private readonly Container $container) {}

    /**
     * Every registered report, keyed by its `key()`.
     *
     * @return array<string, ReportInterface>
     */
    public function all(): array
    {
        if ($this->reports === null) {
            $this->reports = [];

            /** @var list<class-string<ReportInterface>> $classes */
            $classes = config('reporting.reports', []);

            foreach ($classes as $class) {
                $report = $this->container->make($class);
                $this->reports[$report->key()] = $report;
            }
        }

        return $this->reports;
    }

    /**
     * Resolve a report by its key, or null if no such report is registered.
     */
    public function resolve(string $key): ?ReportInterface
    {
        return $this->all()[$key] ?? null;
    }
}
