<?php

namespace App\Factory;

use App\Entity\TimeTracker;
use App\DTO\TimeTrackerDTO;

class TimeTrackerFactory implements FactoryInterface
{
    public function create(TimeTrackerDTO $dto, $user): TimeTracker
    {
        $timeTracker = new TimeTracker();
        $timeTracker->setUser($user);
        $timeTracker->setStartDate(new \DateTime($dto->getStartDate()));
        $timeTracker->setStartTime(new \DateTime($dto->getStartTime()));
        $timeTracker->setEndTime(new \DateTime($dto->getEndTime()));

        return $timeTracker;
    }
}
