<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\TimeTrackerDTO;
use App\Entity\TimeTracker;
use App\Entity\User;

interface TimeTrackerServiceInterface
{
    public function getTimeTrackersByUser(User $user): array;
    public function validateAndPersist(TimeTrackerDTO $timeTrackerDto, User $user): Result;
    public function findTimeTrackerOrThrow(User $user, int $id): TimeTracker;
    public function updateTimeTracker(TimeTrackerDTO $timeTrackerDto, TimeTracker $timeTracker, User $user): Result;
    public function deleteTimeTracker(User $user, int $id): void;
}
