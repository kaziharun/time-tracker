<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ResultDTO;
use App\DTO\TimeTrackerDTO;
use App\Entity\TimeTracker;
use App\Entity\User;

interface TimeTrackerServiceInterface
{
    /**
     * @return array<TimeTracker>
     */
    public function getTimeTrackersByUser(User $user): array;

    public function validateAndPersist(TimeTrackerDTO $timeTrackerDto, User $user): ResultDTO;

    public function findTimeTrackerOrThrow(User $user, int $timeTrackerId): TimeTracker;

    public function updateTimeTracker(TimeTrackerDTO $timeTrackerDto, TimeTracker $timeTracker, User $user): ResultDTO;

    public function deleteTimeTracker(User $user, int $timeTrackerId): void;
}
