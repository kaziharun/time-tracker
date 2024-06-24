<?php

namespace App\Factory;

use App\Service\CSVExporter;
use App\Service\ExporterInterface;

class ExporterFactory
{
    public function create(string $format): ExporterInterface
    {
        return match ($format) {
            'csv' => new CSVExporter(),

            default => throw new \InvalidArgumentException("Unsupported format: $format"),
        };
    }
}
