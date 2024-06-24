<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\TimeTracker;
use App\Repository\TimeTrackerRepository;

class TimeTrackerValidator
{
    public function __construct(private readonly TimeTrackerRepository $timeTrackerRepository)
    {
    }

    public function isEndTimeInvalid(TimeTracker $timeTracker): bool
    {
        if (is_null($timeTracker->getEndTime())) {
            return false;
        }

        return $timeTracker->getStartTime() > $timeTracker->getEndTime();
    }

    public function hasOverlappingEntries(TimeTracker $timeTracker): bool
    {
        $overlaps = $this->findOverlappingEntries($timeTracker);

        return !empty($overlaps);
    }

    /**
     * @return array<TimeTracker>
     */
    public function findOverlappingEntries(TimeTracker $timeTracker): array
    {
        $user = $timeTracker->getUser();
        if (null === $user) {
            throw new \InvalidArgumentException('The time tracker entry must have an associated user.');
        }

        return $this->timeTrackerRepository->findOverlappingEntries(
            $timeTracker->getStartDate(),
            $timeTracker->getStartTime(),
            $timeTracker->getEndTime(),
            $user->getId()
        );
    }
}
