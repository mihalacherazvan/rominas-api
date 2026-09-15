<?php

declare(strict_types=1);

namespace Rominas\Reporting\Enums;

/**
 * The downloadable formats a report can be exported as. Backs the `?format=` query parameter on the
 * export endpoint and supplies the matching filename extension and HTTP content type.
 */
enum ReportFormat: string
{
    case Csv = 'csv';
    case Xlsx = 'xlsx';

    public function contentType(): string
    {
        return match ($this) {
            self::Csv => 'text/csv',
            self::Xlsx => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        };
    }
}
