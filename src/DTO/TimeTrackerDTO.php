<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Project;

class TimeTrackerDTO
{
    public function __construct(
        private readonly ?string $name,
        private readonly ?Project $project,
        private readonly string $startDate,
        private readonly string $startTime,
        private readonly ?string $endTime
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getStartTime(): string
    {
        return $this->startTime;
    }

    public function getEndTime(): ?string
    {
        return $this->endTime;
    }
}
