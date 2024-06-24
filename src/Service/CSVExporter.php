<?php

namespace App\Service;

use App\DTO\ExporterDTO;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CSVExporter implements ExporterInterface
{
    public function export(ExporterDTO $exporterDto): Response
    {
        return new StreamedResponse(function () use ($exporterDto): void {
            $this->outputCSV($exporterDto->getData());
        });
    }

    /**
     * @param array<int, array{
     *     project_name: string,
     *     user_name: string,
     *     daily_hours: array<string, float>,
     *     weekly_hours: array<string, float>,
     *     monthly_hours: array<string, float>
     * }> $data
     */
    private function outputCSV(array $data): void
    {
        $output = fopen('php://output', 'w');
        if (false === $output) {
            throw new \RuntimeException('Failed to open output stream');
        }

        $this->validateResource($output);

        fputcsv($output, ['ID', 'Type', 'User Name', 'Project Name', 'Date', 'Hours']);
        $i = 1;

        foreach ($data as $projectData) {
            $i = $this->writeProjectHours($output, $projectData, $i);
        }

        fclose($output);
    }

    /**
     * @param resource $output
     * @param array{
     *     project_name: string,
     *     user_name: string,
     *     daily_hours: array<string, float>,
     *     weekly_hours: array<string, float>,
     *     monthly_hours: array<string, float>
     *  } $projectData
     */
    private function writeProjectHours($output, array $projectData, int $i): int
    {
        $this->validateResource($output);

        $projectName = $projectData['project_name'];
        $userName = $projectData['user_name'];

        foreach (['daily_hours', 'weekly_hours', 'monthly_hours'] as $type) {
            foreach ($projectData[$type] as $date => $hours) {
                fputcsv($output, [$i++, $type, $userName, $projectName, $date, $hours]);
            }
        }

        return $i;
    }

    /**
     * @param resource $output
     */
    public function validateResource($output): void
    {
        if (!is_resource($output)) {
            throw new \InvalidArgumentException('Output must be a resource.');
        }
    }
}
