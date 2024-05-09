<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\ProjectDTO;
use App\Entity\Project;

interface ProjectServiceInterface
{
    public function getAllProjects(): array;
    public function validateAndPersist(ProjectDTO $projectDto): Result;
    public function updateProject(ProjectDTO $projectDto, Project $project): Result;
    public function findProjectOrThrow(int $id): Project;
    public function deleteProject(int $id): void;
}
