<?php

namespace App\DTO;

class ExporterDTO
{
    /**
     * @param array<int, array{
     *     project_name: string,
     *     user_name: string,
     *     daily_hours: array<string, float>,
     *     weekly_hours: array<string, float>,
     *     monthly_hours: array<string, float>
     * }> $data
     */
    public function __construct(private array $data)
    {
    }

    /**
     * @return array<int, array{
     *     project_name: string,
     *     user_name: string,
     *     daily_hours: array<string, float>,
     *     weekly_hours: array<string, float>,
     *     monthly_hours: array<string, float>
     * }>
     */
    public function getData(): array
    {
        return $this->data;
    }
}
