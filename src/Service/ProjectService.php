<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectService implements ProjectServiceInterface
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }
    public function validateAndPersist(Project $project): Result
    {
        //add validation later
        $this->entityManager->persist($project);
        $this->entityManager->flush();
        return new Result(true, 'Project created Successfully.');
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

        ///I will add validation later

        if (!$project) {
            throw new NotFoundHttpException('The project does not exist.');
        }

        $this->entityManager->remove($project);
        $this->entityManager->flush();
    }
}
