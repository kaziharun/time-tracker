<?php
declare(strict_types=1);

namespace App\Factory;

use App\Entity\TimeTracker;
use App\DTO\TimeTrackerDTO;
use App\Entity\User;
use DateTimeImmutable;

class TimeTrackerFactory
{
    public function createOrUpdate(TimeTrackerDTO $dto, User $user, ?TimeTracker $timeTracker = null): TimeTracker
    {
        $timeTracker = $timeTracker ?? new TimeTracker();

        $timeTracker->setUser($user);
        $timeTracker->setName($dto->getName());
        $timeTracker->setProject($dto->getProject());

        $startDate = $this->createDateTimeImmutable($dto->getStartDate());
        $startTime = $this->createDateTimeImmutable($dto->getStartTime());
        $endTime = $this->createDateTimeImmutable($dto->getEndTime());

        $timeTracker->setStartDate($startDate);
        $timeTracker->setStartTime($startTime);
        $timeTracker->setEndTime($endTime);

        return $timeTracker;
    }

    private function createDateTimeImmutable(?string $dateTimeString): ?DateTimeImmutable
    {
        return $dateTimeString ? new DateTimeImmutable($dateTimeString) : null;
    }
}
