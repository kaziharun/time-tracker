<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\TimeTrackerDTO;
use App\Entity\TimeTracker;
use App\Entity\User;
use App\Factory\TimeTrackerFactory;
use App\Repository\TimeTrackerRepository;
use App\Validator\TimeTrackerValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TimeTrackerService implements TimeTrackerServiceInterface
{
    public function __construct(
        private TimeTrackerRepository  $timeTrackerRepository,
        private TimeTrackerValidator   $validator,
        private EntityManagerInterface $entityManager,
        private TimeTrackerFactory     $timeTrackerFactory
    )
    {
    }

    public function getTimeTrackersByUser(User $user): array
    {
        return $this->timeTrackerRepository->findByUser($user->getId());
    }

    public function validateAndPersist(TimeTrackerDTO $timeTrackerDto, User $user): Result
    {
        $timeTracker = $this->timeTrackerFactory->createOrUpdate($timeTrackerDto, $user);

        if ($this->validator->isEndTimeInvalid($timeTracker)) {
            return Result::Failed('End time cannot be earlier than start time.');
        }

        if ($this->validator->hasOverlappingEntries($timeTracker)) {

            return Result::Failed('There is an overlapping entry.');
        }

        $this->entityManager->persist($timeTracker);
        $this->entityManager->flush();

        return Result::Success('Time tracker created Successfully.');
    }

    public function findTimeTrackerOrThrow(User $user, int $id): TimeTracker
    {
        $timeTracker = $this->timeTrackerRepository->findOneBy(
            [
                'id' => $id,
                'user' => $user->getId()
            ]
        );

        if (!$timeTracker) {
            throw new NotFoundHttpException('The time tracker does not exist.');
        }
        return $timeTracker;
    }

    public function updateTimeTracker(TimeTrackerDTO $timeTrackerDto, TimeTracker $timeTracker, User $user): Result
    {
        $timeTracker = $this->timeTrackerFactory->createOrUpdate($timeTrackerDto, $user, $timeTracker);

        if ($this->validator->isEndTimeInvalid($timeTracker)) {
            return Result::Failed('End time cannot be earlier than start time.');
        }

        $overlaps = $this->validator->findOverlappingEntries($timeTracker);

        foreach ($overlaps as $overlap) {
            if ($overlap->getId() !== $timeTracker->getId()) {
                return Result::Failed( 'There is an overlapping entry.');
            }
        }

        $this->entityManager->persist($timeTracker);
        $this->entityManager->flush();

        return Result::Success('Time tracker has been updated.');
    }

    public function deleteTimeTracker(User $user, int $id): void
    {
        $timeTracker = $this->timeTrackerRepository->find($id);

        if (!$timeTracker) {
            throw new NotFoundHttpException('The time tracker does not exist.');
        }

        if($user->getId() !== $timeTracker->getUser()->getId()){
            throw new AccessDeniedHttpException('You do not have permission to delete this time tracker.');
        }

        $this->entityManager->remove($timeTracker);
        $this->entityManager->flush();
    }
}
