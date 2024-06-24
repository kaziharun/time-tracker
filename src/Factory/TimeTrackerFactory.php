<?php

declare(strict_types=1);

namespace App\Factory;

use App\DTO\TimeTrackerDTO;
use App\Entity\TimeTracker;
use App\Entity\User;

class TimeTrackerFactory
{
    public function createOrUpdate(TimeTrackerDTO $dto, User $user, ?TimeTracker $timeTracker = null): TimeTracker
    {
        $timeTracker = $timeTracker ?? new TimeTracker();

        $timeTracker->setUser($user);
        $timeTracker->setName($dto->getName());
        $timeTracker->setProject($dto->getProject());

        $startDate = $this->createDateTime($dto->getStartDate());
        $startTime = $this->createDateTime($dto->getStartTime());
        $endTime = $this->createDateTime($dto->getEndTime());

        if ($startDate) {
            $timeTracker->setStartDate($startDate);
        }

        if ($startTime) {
            $timeTracker->setStartTime($startTime);
        }

        $timeTracker->setEndTime($endTime);

        return $timeTracker;
    }

    private function createDateTime(?string $dateTimeString): ?\DateTimeInterface
    {
        return $dateTimeString ? new \DateTime($dateTimeString) : null;
    }
}
