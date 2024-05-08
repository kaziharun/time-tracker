<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\TimeTracker;
use App\Repository\TimeTrackerRepository;
use App\Validator\TimeTrackerValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TimeTrackerService
{
    public function __construct(
        private TimeTrackerRepository $timeTrackerRepository,
        private TimeTrackerValidator $validator,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function getTimeTrackersForUser(int $userId): array
    {
        return $this->timeTrackerRepository->findByUser($userId);
    }

    public function validateAndPersist(TimeTracker $timeTracker): Result
    {
        if ($this->validator->isEndTimeInvalid($timeTracker)) {
            return new Result(false, 'End time cannot be earlier than start time.');
        }

        if ($this->validator->hasOverlappingEntries($timeTracker)) {
            return new Result(false, 'There is an overlapping entry.');
        }

        $this->entityManager->persist($timeTracker);
        $this->entityManager->flush();

        return new Result(true, 'Time tracker created Successfully.');
    }

    public function findTimeTrackerOrThrow(int $id): TimeTracker
    {
        $timeTracker = $this->timeTrackerRepository->find($id);
        if (!$timeTracker) {
            throw new NotFoundHttpException('The time tracker does not exist.');
        }
        return $timeTracker;
    }

    public function updateTimeTracker(TimeTracker $timeTracker): Result
    {
        if ($this->validator->isEndTimeInvalid($timeTracker)) {
            return new Result(false, 'End time cannot be earlier than start time.');
        }

        $overlaps = $this->validator->findOverlappingEntries($timeTracker);

        foreach ($overlaps as $overlap) {
            if ($overlap->getId() !== $timeTracker->getId()) {
                return new Result(false, 'There is an overlapping entry.');
            }
        }

        $this->entityManager->persist($timeTracker);
        $this->entityManager->flush();

        return new Result(true, 'Time tracker has been updated.');
    }

    public function deleteTimeTracker(int $id): void
    {
        $timeTracker = $this->timeTrackerRepository->find($id);

        if (!$timeTracker) {
            throw new NotFoundHttpException('The time tracker does not exist.');
        }

        $this->entityManager->remove($timeTracker);
        $this->entityManager->flush();
    }
}
