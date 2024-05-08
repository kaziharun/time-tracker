<?php
declare(strict_types=1);
namespace App\DTO;

class TimeTrackerDTO implements DTOInterface
{
    private ?string $startDate;
    private ?string $startTime;
    private ?string $endTime;

    public function __construct(array $data)
    {
        $this->startDate = $data['startDate'] ?? null;
        $this->startTime = $data['startTime'] ?? null;
        $this->endTime = $data['endTime'] ?? null;

    }

    public function getEndTime(): mixed
    {
        return $this->endTime;
    }

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function getStartTime(): ?string
    {
        return $this->startTime;
    }
}

