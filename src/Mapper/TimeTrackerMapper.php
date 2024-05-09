<?php
declare(strict_types=1);

namespace App\Mapper;

use App\Entity\TimeTracker;
use App\DTO\TimeTrackerDTO;

class TimeTrackerMapper
{
    public function transform(TimeTracker $timeTracker): TimeTrackerDTO
    {
        return new TimeTrackerDTO(
            name: $timeTracker->getName(),
            project: $timeTracker->getProject(),
            startDate: $timeTracker->getStartDate()->format('Y-m-d'),
            startTime: $timeTracker->getStartTime()->format('H:i:s'),
            endTime: $timeTracker->getEndTime() ? $timeTracker->getEndTime()->format('H:i:s') : null,
        );
    }
}
