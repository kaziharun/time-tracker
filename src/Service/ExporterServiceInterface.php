<?php

namespace App\Service;

use App\DTO\ExporterDTO;
use Symfony\Component\HttpFoundation\Response;

interface ExporterServiceInterface
{
    public function export(ExporterDTO $data, string $format): Response;
}
