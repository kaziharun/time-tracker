<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProjectDTO;
use App\DTO\ResultDTO;
use App\Entity\Project;

interface ProjectServiceInterface
{
    /**
     * @return array<Project>
     */
    public function getAll(): array;

    public function validateAndPersist(ProjectDTO $projectDto): ResultDTO;

    public function update(ProjectDTO $projectDto, Project $project): ResultDTO;

    public function findOrThrow(int $projectId): Project;

    public function delete(int $projectId): void;
}
