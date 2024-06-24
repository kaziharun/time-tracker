<?php

namespace App\Service;

use App\DTO\ExporterDTO;
use App\Factory\ExporterFactory;
use Symfony\Component\HttpFoundation\Response;

class ExporterService implements ExporterServiceInterface
{
    public function __construct(private readonly ExporterFactory $exporterFactory)
    {
    }

    public function export(ExporterDTO $data, string $format): Response
    {
        $exporter = $this->exporterFactory->create($format);
        $response = $exporter->export($data);

        $this->setFormatAndHeaders($response, $format);

        return $response;
    }

    private function generateFilename(): string
    {
        $timestamp = (new \DateTime())->format('YmdHis');

        return sprintf('time_tracker_export_%d', $timestamp);
    }

    private function setCSVResponseHeaders(Response $response): void
    {
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$this->generateFilename().'.csv"');
    }

    private function setFormatAndHeaders(Response $response, string $format): void
    {
        $response->setStatusCode(Response::HTTP_OK);

        switch ($format) {
            case 'csv':
                $this->setCSVResponseHeaders($response);
                break;
            default:
                throw new \InvalidArgumentException("Unsupported format: $format");
        }
    }
}
