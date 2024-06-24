<?php

declare(strict_types=1);

namespace App\Mapper;

use App\DTO\TimeTrackerDTO;
use App\Entity\TimeTracker;

class TimeTrackerMapper
{
    public function transform(TimeTracker $timeTracker): TimeTrackerDTO
    {
        return new TimeTrackerDTO(
            name: $timeTracker->getName(),
            project: $timeTracker->getProject(),
            startDate: $timeTracker->getStartDate()->format('Y-m-d'),
            startTime: $timeTracker->getStartTime()->format('H:i:s'),
            endTime: $timeTracker->getEndTime()?->format('H:i:s'),
        );
    }
}
