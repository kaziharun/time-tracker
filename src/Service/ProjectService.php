<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProjectDTO;
use App\DTO\ResultDTO;
use App\Entity\Project;
use App\Factory\ProjectFactory;
use App\Repository\ProjectRepository;
use App\Repository\TimeTrackerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectService implements ProjectServiceInterface
{
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly TimeTrackerRepository $timeTrackerRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ProjectFactory $projectFactory
    ) {
    }

    /**
     * @return array<Project>
     */
    public function getAll(): array
    {
        return $this->projectRepository->findAll();
    }

    public function validateAndPersist(ProjectDTO $projectDto): ResultDTO
    {
        $project = $this->projectFactory->createOrUpdate($projectDto);

        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return ResultDTO::Success('Project created Successfully.');
    }

    public function update(ProjectDTO $projectDto, Project $project): ResultDTO
    {
        $project = $this->projectFactory->createOrUpdate($projectDto, $project);

        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return ResultDTO::Success('Project has been updated.');
    }

    public function findOrThrow(int $projectId): Project
    {
        $project = $this->projectRepository->find($projectId);

        if (!$project) {
            throw new NotFoundHttpException('The project does not exist.');
        }

        return $project;
    }

    public function delete(int $projectId): void
    {
        $project = $this->projectRepository->find($projectId);

        $timeTracker = $this->timeTrackerRepository->findOneBy(['project' => $project]);

        if ($timeTracker) {
            throw new ConflictHttpException('The project already used in Time Tracker.');
        }

        if (!$project) {
            throw new NotFoundHttpException('The project does not exist.');
        }

        $this->entityManager->remove($project);
        $this->entityManager->flush();
    }
}
