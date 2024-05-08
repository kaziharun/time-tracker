<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\TimeTracker;

interface TimeTrackerServiceInterface
{
    public function getTimeTrackersForUser(int $userId): array;
    public function validateAndPersist(TimeTracker $timeTracker): Result;
    public function findTimeTrackerOrThrow(int $id): TimeTracker;
    public function updateTimeTracker(TimeTracker $timeTracker): Result;
    public function deleteTimeTracker(int $id): void;
}
