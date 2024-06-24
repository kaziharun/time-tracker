<?php

namespace App\Service;

use App\DTO\ExporterDTO;
use Symfony\Component\HttpFoundation\Response;

interface ExporterInterface
{
    public function export(ExporterDTO $exporterDto): Response;
}
