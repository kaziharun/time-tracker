<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\ProjectDTO;
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
        private ProjectRepository      $projectRepository,
        private TimeTrackerRepository  $timeTrackerRepository,
        private EntityManagerInterface $entityManager,
        private ProjectFactory         $projectFactory
    )
    {
    }

    public function getAllProjects(): array
    {
        return $this->projectRepository->findAll();
    }

    public function validateAndPersist(ProjectDTO $projectDto): Result
    {
        $project = $this->projectFactory->createOrUpdate($projectDto);

        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return Result::Success('Project created Successfully.');
    }

    public function updateProject(ProjectDTO $projectDto, Project $project): Result
    {
        $project = $this->projectFactory->createOrUpdate($projectDto, $project);

        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return Result::Success( 'Time tracker has been updated.');
    }

    public function findProjectOrThrow(int $id): Project
    {
        $project = $this->projectRepository->find($id);

        if (!$project) {
            throw new NotFoundHttpException('The project does not exist.');
        }

        return $project;
    }

    public function deleteProject(int $id): void
    {
        $project = $this->projectRepository->find($id);

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
