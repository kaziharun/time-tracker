<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeConversionExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('convertDecimalToHoursMinutes', $this->convertDecimalToHoursMinutes(...)),
        ];
    }

    public function convertDecimalToHoursMinutes(float $decimalHours): string
    {
        $hours = floor($decimalHours);
        $minutes = round(($decimalHours - $hours) * 60);

        return sprintf('%d hours %d minutes', $hours, $minutes);
    }
}
